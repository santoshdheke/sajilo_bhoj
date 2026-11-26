<?php

namespace Modules\Restaurant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\EarningTransaction;
use Modules\Restaurant\Models\Setting;
use Modules\Restaurant\Models\WithdrawalRequest;
use Modules\Restaurant\Support\Billing;
use Modules\Restaurant\Support\ReferralEarnings;
use Modules\Restaurant\Support\RestaurantSignup;
use Modules\Restaurant\Support\Tenancy;

/**
 * Refer & Earn: add client restaurants and earn a bonus (billing.referral_bonus_percent) every time
 * one of them tops up. Bonuses collect as earnings; the company moves them into its plan balance
 * or withdraws them (see Support\ReferralEarnings).
 */
class ReferralController extends Controller
{
    public function index(Request $request): View
    {
        $company = Tenancy::company();

        $clients = Company::where('referred_by_company_id', $company->id)
            ->with(['domains' => fn ($q) => $q->orderByDesc('is_primary')])
            ->latest('id')
            ->paginate(10);

        // Bonus earned from each client, and in total.
        $earned = EarningTransaction::where('type', 'bonus')
            ->selectRaw('source_company_id, SUM(amount) AS total, COUNT(*) AS payments')
            ->groupBy('source_company_id')
            ->get()
            ->keyBy('source_company_id');

        return view('restaurant::admin.theme_one.subscription.referrals', [
            'company'      => Company::find($company->id), // fresh earnings
            'clients'      => $clients,
            'earned'       => $earned,
            'totalEarned'  => (float) $earned->sum('total'),
            'pendingOut'   => (float) WithdrawalRequest::where('status', 'pending')->sum('amount'),
            'withdrawn'    => (float) WithdrawalRequest::where('status', 'paid')->sum('amount'),
            'history'      => EarningTransaction::with(['source', 'withdrawal'])->latest('id')->limit(10)->get(),
            'clientCount'  => $clients->total(),
            'payingCount'  => $earned->whereNotNull('source_company_id')->count(),
            'percent'      => Billing::referralPercent(),
            'minWithdraw'  => ReferralEarnings::minWithdrawal(),
            'methods'      => WithdrawalRequest::METHODS,
            // New clients start with your own brand colour (they can change it later).
            'ownColor'     => Setting::getValue($company->id, 'theme_color') ?: '#198754',
            'domainSuffix' => $this->domainSuffix($request->getHost()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['domain' => Str::lower(trim((string) $request->input('domain'), " \t\n\r\0\x0B/"))]);
        $request->merge(['domain' => preg_replace('#^https?://#', '', $request->input('domain'))]);

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'domain'       => ['required', 'string', 'max:253', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/', 'unique:domains,domain'],
            'theme_color'  => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|max:255|unique:companies,email|unique:restaurant.users,email',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'domain.regex'       => 'Enter a web address like newplace.example.com (no http:// or slashes).',
            'domain.unique'      => 'This web address is already used by another workspace.',
            'owner_email.unique' => 'This email is already used by another workspace.',
        ], [
            'company_name' => 'restaurant name',
            'domain'       => 'web address',
        ]);

        RestaurantSignup::create($data, $data['domain'], Tenancy::company());

        return redirect()->route('admin.subscription.referrals.index')
            ->with('status', "{$data['company_name']} is ready. Its owner can sign in at {$data['domain']}/admin/login with {$data['owner_email']}.");
    }

    /** Move earnings into the plan balance. */
    public function moveToBalance(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('toBalance', ['amount' => 'required|numeric|min:1']);

        try {
            ReferralEarnings::moveToBalance(Tenancy::company(), round((float) $data['amount'], 2), $request->user()->name);
        } catch (ValidationException $e) {
            throw $e->errorBag('toBalance');
        }

        return redirect()->route('admin.subscription.referrals.index')
            ->with('status', 'Nrs ' . number_format((float) $data['amount'], 2) . ' added to your plan balance.');
    }

    /** Ask the SajiloBhoj team to pay out earnings to eSewa / Khalti / bank. */
    public function withdraw(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('withdraw', [
            'amount'         => 'required|numeric|min:' . ReferralEarnings::minWithdrawal(),
            'method'         => 'required|in:' . implode(',', array_keys(WithdrawalRequest::METHODS)),
            'account_name'   => 'required|string|max:120',
            'account_number' => ['required', 'string', 'max:40', $request->input('method') === 'bank' ? 'regex:/^[0-9A-Za-z\- ]+$/' : 'regex:/^9[678][0-9]{8}$/'],
            'bank_name'      => 'required_if:method,bank|nullable|string|max:120',
        ], [
            'amount.min'              => 'You can withdraw at least Nrs ' . number_format(ReferralEarnings::minWithdrawal()) . ' at a time.',
            'account_number.regex'    => $request->input('method') === 'bank' ? 'Enter the bank account number.' : 'Enter the 10-digit mobile number of the eSewa / Khalti account.',
            'bank_name.required_if'   => 'Enter the bank name.',
        ], ['account_number' => $request->input('method') === 'bank' ? 'account number' : 'eSewa / Khalti ID']);

        try {
            $withdrawal = ReferralEarnings::requestWithdrawal(Tenancy::company(), $data, $request->user()->id);
        } catch (ValidationException $e) {
            throw $e->errorBag('withdraw');
        }

        $this->notifyTeam($withdrawal);

        return redirect()->route('admin.subscription.referrals.index')
            ->with('status', 'Withdrawal of Nrs ' . number_format((float) $withdrawal->amount, 2) . ' requested to ' . $withdrawal->destination()
                . '. The SajiloBhoj team will send it soon.');
    }

    private function notifyTeam(WithdrawalRequest $withdrawal): void
    {
        $company = Tenancy::company();
        $text = "Refer & Earn withdrawal #{$withdrawal->id}\n"
            . "Company: {$company->name} (#{$company->id})\n"
            . 'Amount: Nrs ' . number_format((float) $withdrawal->amount, 2) . "\n"
            . "Pay to: {$withdrawal->destination()} ({$withdrawal->account_name})\n\n"
            . "After paying: php artisan referrals:withdrawals --paid={$withdrawal->id} --reference=<transaction id>";

        Log::info($text);
        if ($to = config('restaurant.billing.payout_notify_email')) {
            try {
                Mail::raw($text, fn ($m) => $m->to($to)->subject("Withdrawal request #{$withdrawal->id} — {$company->name}"));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /** ".example.com" from "first.example.com", to suggest a client address on the same domain. */
    private function domainSuffix(string $host): ?string
    {
        $parts = explode('.', $host);

        return count($parts) > 2 ? '.' . implode('.', array_slice($parts, 1)) : null;
    }
}
