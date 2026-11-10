<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\TopupPayment;
use App\Models\WalletTransaction;
use App\Support\Billing;
use App\Support\Payments\Esewa;
use App\Support\Payments\Khalti;
use App\Support\Payments\Topups;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function choose(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.theme_one.subscription.choose', ['plans' => $plans]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::where('is_active', true)->findOrFail($data['plan_id']);
        $sub  = Billing::choosePlan(Tenancy::company(), $plan);

        if ($sub->isExpired()) {
            return redirect()->route('admin.subscription.topup')
                ->with('status', "You're on the {$plan->name} plan. Top up your balance to start it.");
        }

        return redirect()->route('admin.dashboard.index')
            ->with('status', $sub->isTrialing()
                ? "You're on the {$plan->name} plan — {$sub->trialDaysLeft()} days free."
                : "You're on the {$plan->name} plan.");
    }

    /** Balance, rate, top-up form and history. */
    public function topup(): View
    {
        $company = Tenancy::company();
        $sub     = Billing::settle($company);
        $company->refresh();

        return view('admin.theme_one.subscription.topup', [
            'company'      => $company,
            'sub'          => $sub,
            'plan'         => $sub?->plan,
            'runway'       => Billing::runwayDays($company, $sub?->plan),
            'transactions' => request()->boolean('all')
                ? WalletTransaction::latest('id')->paginate(20)->withQueryString()
                : WalletTransaction::latest('id')->limit(5)->get(),
            'showAll'      => request()->boolean('all'),
            'gateways'     => Topups::available(),
            'min'          => config('billing.min_topup'),
            'max'          => config('billing.max_topup'),
        ]);
    }

    /** Start a top-up: create the payment record and send the person to Khalti or eSewa. */
    public function storeTopup(Request $request)
    {
        $data = $request->validate([
            'amount'  => ['required', 'numeric', 'min:' . config('billing.min_topup'), 'max:' . config('billing.max_topup')],
            'gateway' => ['required', 'in:' . implode(',', array_keys(Topups::available()) ?: ['none'])],
        ], ['gateway.required' => 'Choose Khalti or eSewa.', 'gateway.in' => 'Choose Khalti or eSewa.']);

        $company = Tenancy::company();
        $payment = Topups::start($company, $data['gateway'], (float) $data['amount'], $request->user()->id);

        if ($payment->gateway === 'esewa') {
            return view('admin.theme_one.subscription.esewa-redirect', [
                'action' => config('billing.esewa.form_url'),
                'fields' => Esewa::formFields($payment,
                    route('admin.subscription.topup.esewa.success'),
                    route('admin.subscription.topup.esewa.failure', ['ref' => $payment->reference])),
            ]);
        }

        try {
            $url = Khalti::initiate($payment, route('admin.subscription.topup.khalti.return'), url('/'), [
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['gateway' => $e instanceof \RuntimeException ? $e->getMessage() : 'Could not reach Khalti. Please try again.']);
        }

        return redirect()->away($url);
    }

    /** Khalti sends the person back here; the payment is verified with Khalti's lookup API. */
    public function khaltiReturn(Request $request): RedirectResponse
    {
        $payment = TopupPayment::where('gateway', 'khalti')->where('gateway_token', (string) $request->query('pidx'))->first();
        if (! $payment) {
            return $this->topupResult('error', 'We couldn’t find that payment.');
        }

        $lookup = Khalti::lookup($payment->gateway_token);
        $status = $lookup['status'] ?? null;

        if ($status === 'Completed' && (int) ($lookup['total_amount'] ?? 0) === (int) round($payment->amount * 100)) {
            Topups::complete($payment, $lookup['transaction_id'] ?? null, $lookup);

            return $this->topupResult('status', 'Payment received — Nrs ' . number_format((float) $payment->amount, 2) . ' added to your balance.');
        }
        // Still processing, or Khalti couldn't be reached: keep it pending, nothing is lost by checking again.
        if ($status === null || in_array($status, ['Pending', 'Initiated'], true)) {
            return $this->topupResult('error', 'Khalti hasn’t confirmed the payment yet. Refresh this page in a minute.');
        }

        Topups::fail($payment, $status === 'User canceled' ? 'cancelled' : 'failed', $lookup);

        return $this->topupResult('error', $status === 'User canceled' ? 'Payment cancelled. No money was taken.' : 'Payment was not completed. No balance was added.');
    }

    /** eSewa success: check the signed data, then confirm with eSewa's status API. */
    public function esewaSuccess(Request $request): RedirectResponse
    {
        $data    = Esewa::decode($request->query('data'));
        $payment = $data ? TopupPayment::where('gateway', 'esewa')->where('reference', $data['transaction_uuid'] ?? '')->first() : null;
        if (! $payment) {
            return $this->topupResult('error', 'We couldn’t verify that eSewa payment.');
        }

        $check = Esewa::status($payment);
        if (($data['status'] ?? null) === 'COMPLETE' && ($check['status'] ?? null) === 'COMPLETE'
            && abs((float) ($check['total_amount'] ?? 0) - (float) $payment->amount) < 0.01) {
            Topups::complete($payment, $check['ref_id'] ?? ($data['transaction_code'] ?? null), ['callback' => $data, 'status' => $check]);

            return $this->topupResult('status', 'Payment received — Nrs ' . number_format((float) $payment->amount, 2) . ' added to your balance.');
        }

        Topups::fail($payment, 'failed', ['callback' => $data, 'status' => $check]);

        return $this->topupResult('error', 'eSewa did not confirm the payment. No balance was added.');
    }

    public function esewaFailure(Request $request): RedirectResponse
    {
        if ($payment = TopupPayment::where('gateway', 'esewa')->where('reference', (string) $request->query('ref'))->first()) {
            Topups::fail($payment, 'cancelled');
        }

        return $this->topupResult('error', 'Payment cancelled. No money was taken.');
    }

    private function topupResult(string $type, string $message): RedirectResponse
    {
        return redirect()->route('admin.subscription.topup')->with($type, $message);
    }
}
