<?php

namespace Modules\Restaurant\Console\Commands;

use Illuminate\Console\Command;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\WithdrawalRequest;
use Modules\Restaurant\Support\ReferralEarnings;

/**
 * For the SajiloBhoj team: pay out Refer & Earn withdrawals.
 *
 *   php artisan referrals:withdrawals                          list pending requests
 *   php artisan referrals:withdrawals --all                    list every request
 *   php artisan referrals:withdrawals --paid=12 --reference=TX after sending the money
 *   php artisan referrals:withdrawals --reject=12 --reason="Wrong eSewa ID"   amount goes back to their earnings
 */
class ReferralWithdrawals extends Command
{
    protected $signature = 'referrals:withdrawals
        {--all : Show every request, not only pending ones}
        {--paid= : Mark this request id as paid}
        {--reference= : Payout transaction id (with --paid)}
        {--reject= : Reject this request id (the amount goes back to the company\'s earnings)}
        {--reason= : Why it was rejected (shown to the company)}';

    protected $description = 'List Refer & Earn withdrawal requests and mark them paid or rejected';

    public function handle(): int
    {
        try {
            if ($id = $this->option('paid')) {
                ReferralEarnings::markPaid($this->find($id), $this->option('reference'));
                $this->info("Withdrawal #{$id} marked as paid.");

                return self::SUCCESS;
            }
            if ($id = $this->option('reject')) {
                ReferralEarnings::reject($this->find($id), $this->option('reason'));
                $this->info("Withdrawal #{$id} rejected; the amount is back in the company's earnings.");

                return self::SUCCESS;
            }
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $requests = WithdrawalRequest::withoutGlobalScopes()
            ->when(! $this->option('all'), fn ($q) => $q->where('status', 'pending'))
            ->oldest('id')->get();
        $names = Company::withoutGlobalScopes()->whereIn('id', $requests->pluck('company_id'))->pluck('name', 'id');

        if ($requests->isEmpty()) {
            $this->info($this->option('all') ? 'No withdrawal requests yet.' : 'No pending withdrawals.');

            return self::SUCCESS;
        }

        $this->table(['#', 'Company', 'Amount (Nrs)', 'Pay to', 'Account name', 'Status', 'Requested'], $requests->map(fn ($r) => [
            $r->id, $names[$r->company_id] ?? "#{$r->company_id}", number_format((float) $r->amount, 2), $r->destination(),
            $r->account_name, $r->status . ($r->payout_reference ? " ({$r->payout_reference})" : ''), $r->created_at->format('d M Y H:i'),
        ]));

        return self::SUCCESS;
    }

    private function find(string $id): WithdrawalRequest
    {
        return WithdrawalRequest::withoutGlobalScopes()->find($id) ?? throw new \RuntimeException("No withdrawal request #{$id}.");
    }
}
