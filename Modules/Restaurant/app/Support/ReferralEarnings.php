<?php

namespace Modules\Restaurant\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\EarningTransaction;
use Modules\Restaurant\Models\WithdrawalRequest;

/**
 * Refer & Earn earnings wallet (companies.referral_earnings).
 *
 * - A client's confirmed top-up adds the bonus here (Billing::referralBonus).
 * - The company then chooses: move any amount into its plan balance (instant),
 *   or request a withdrawal to eSewa / Khalti / bank. The amount is held as soon as it is
 *   requested; the SajiloBhoj team pays it and marks it paid, or rejects it and the amount comes back
 *   (php artisan referrals:withdrawals).
 */
class ReferralEarnings
{
    public static function minWithdrawal(): float
    {
        return (float) config('restaurant.billing.referral_min_withdrawal', 100);
    }

    /** Add a bonus earned from $client's top-up. */
    public static function credit(Company $company, float $amount, Company $client, string $note): void
    {
        DB::transaction(function () use ($company, $amount, $client, $note) {
            $company = self::lock($company);
            self::change($company, 'bonus', $amount, $note, sourceCompanyId: $client->id);
        });
    }

    /** Move earnings into the plan balance (pays for the plan; restarts a paused plan). */
    public static function moveToBalance(Company $company, float $amount, string $by): void
    {
        DB::transaction(function () use ($company, $amount, $by) {
            $company = self::lock($company);
            self::ensureEnough($company, $amount);
            self::change($company, 'to_balance', -$amount, "Added to plan balance by {$by}");
            Billing::topUp($company, $amount, 'From referral earnings', 'bonus');
        });
    }

    /** @param array{amount: float, method: string, account_name: string, account_number: string, bank_name?: ?string} $data */
    public static function requestWithdrawal(Company $company, array $data, ?int $userId): WithdrawalRequest
    {
        return DB::transaction(function () use ($company, $data, $userId) {
            $company = self::lock($company);
            $amount  = round((float) $data['amount'], 2);
            self::ensureEnough($company, $amount);

            $request = WithdrawalRequest::withoutGlobalScopes()->create([
                'company_id'     => $company->id,
                'user_id'        => $userId,
                'amount'         => $amount,
                'method'         => $data['method'],
                'account_name'   => $data['account_name'],
                'account_number' => $data['account_number'],
                'bank_name'      => $data['method'] === 'bank' ? ($data['bank_name'] ?? null) : null,
                'status'         => 'pending',
            ]);
            self::change($company, 'withdrawal', -$amount, 'Withdrawal to ' . $request->destination(), withdrawalId: $request->id);

            return $request;
        });
    }

    public static function markPaid(WithdrawalRequest $request, ?string $reference): void
    {
        DB::transaction(function () use ($request, $reference) {
            $request = WithdrawalRequest::withoutGlobalScopes()->lockForUpdate()->findOrFail($request->id);
            if ($request->status !== 'pending') {
                throw new \RuntimeException("Withdrawal #{$request->id} is already {$request->status}.");
            }
            $request->update(['status' => 'paid', 'payout_reference' => $reference, 'processed_at' => now()]);
        });
    }

    /** Reject a pending request: the held amount goes back to the company's earnings. */
    public static function reject(WithdrawalRequest $request, ?string $reason): void
    {
        DB::transaction(function () use ($request, $reason) {
            $request = WithdrawalRequest::withoutGlobalScopes()->lockForUpdate()->findOrFail($request->id);
            if ($request->status !== 'pending') {
                throw new \RuntimeException("Withdrawal #{$request->id} is already {$request->status}.");
            }
            $request->update(['status' => 'rejected', 'admin_note' => $reason, 'processed_at' => now()]);

            $company = self::lock(Company::findOrFail($request->company_id));
            self::change($company, 'refund', (float) $request->amount,
                'Withdrawal not paid' . ($reason ? " · {$reason}" : '') . ' — amount returned', withdrawalId: $request->id);
        });
    }

    private static function lock(Company $company): Company
    {
        return Company::lockForUpdate()->findOrFail($company->id);
    }

    private static function ensureEnough(Company $company, float $amount): void
    {
        if ($amount <= 0 || $amount > (float) $company->referral_earnings + 0.001) {
            throw ValidationException::withMessages([
                'amount' => 'You have Nrs ' . number_format((float) $company->referral_earnings, 2) . ' in earnings.',
            ]);
        }
    }

    /** Apply a signed change to the wallet and log it (amount is stored positive). */
    private static function change(Company $company, string $type, float $delta, string $note, ?int $sourceCompanyId = null, ?int $withdrawalId = null): void
    {
        $company->referral_earnings = round((float) $company->referral_earnings + $delta, 2);
        $company->save();

        EarningTransaction::withoutGlobalScopes()->create([
            'company_id'            => $company->id,
            'type'                  => $type,
            'amount'                => abs(round($delta, 2)),
            'earnings_after'        => $company->referral_earnings,
            'source_company_id'     => $sourceCompanyId,
            'withdrawal_request_id' => $withdrawalId,
            'note'                  => $note,
        ]);
    }
}
