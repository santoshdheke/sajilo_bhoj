<?php

namespace App\Support\Payments;

use App\Models\Company;
use App\Models\TopupPayment;
use App\Support\Billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Shared top-up steps for every gateway. */
class Topups
{
    /** Gateways that have credentials configured (key => label). */
    public static function available(): array
    {
        return array_filter([
            'khalti' => Khalti::configured() ? 'Khalti' : null,
            'esewa'  => Esewa::configured() ? 'eSewa' : null,
        ]);
    }

    public static function start(Company $company, string $gateway, float $amount, ?int $userId): TopupPayment
    {
        return TopupPayment::create([
            'company_id' => $company->id,
            'user_id'    => $userId,
            'gateway'    => $gateway,
            'amount'     => round($amount, 2),
            'status'     => 'pending',
            'reference'  => 'TOPUP-' . $company->id . '-' . Str::upper(Str::random(12)),
        ]);
    }

    /**
     * Mark a verified payment as completed and add it to the balance — exactly once,
     * even if the gateway sends the person back twice or the page is refreshed.
     */
    public static function complete(TopupPayment $payment, ?string $txnId, array $response): bool
    {
        return DB::transaction(function () use ($payment, $txnId, $response) {
            $payment = TopupPayment::withoutGlobalScopes()->lockForUpdate()->find($payment->id);
            if ($payment->status === 'completed') {
                return false;
            }

            $payment->update(['status' => 'completed', 'gateway_txn_id' => $txnId, 'response' => $response, 'completed_at' => now()]);
            Billing::topUp(Company::find($payment->company_id), (float) $payment->amount,
                $payment->gatewayName() . ($txnId ? " · {$txnId}" : ''));

            return true;
        });
    }

    public static function fail(TopupPayment $payment, string $status, array $response = []): void
    {
        if ($payment->status === 'pending') {
            $payment->update(['status' => $status, 'response' => $response ?: $payment->response]);
        }
    }
}
