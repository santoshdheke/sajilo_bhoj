<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Prepaid subscriptions.
 *
 * - First plan: free trial (plan.trial_days). No charges while it runs.
 * - After the trial: the plan's hourly rate (monthly price / 720 h) is taken from the company balance.
 *   Charges are booked per whole hour, so the history stays short and readable.
 * - Balance can't cover the next hour: subscription becomes "expired" (admin asks for a top-up,
 *   website shows "coming soon"). A top-up starts it again from that moment.
 */
class Billing
{
    /** Bring the company's subscription up to date: end a finished trial, take what's owed. */
    public static function settle(Company $company): ?Subscription
    {
        return DB::transaction(function () use ($company) {
            $company = Company::lockForUpdate()->find($company->id);
            $sub = Subscription::withoutGlobalScope('company')->with('plan')
                ->where('company_id', $company->id)->latest('id')->lockForUpdate()->first();

            if (! $sub || in_array($sub->status, ['cancelled', 'expired'], true) || $sub->isTrialing()) {
                return $sub;
            }

            // Trial just ended: billing starts at the end of the trial.
            if ($sub->status === 'trialing') {
                $sub->status = 'active';
                $sub->last_charged_at = $sub->trial_ends_at;
            }

            $from  = $sub->last_charged_at ?? $sub->starts_at ?? now();
            $hours = (int) floor($from->diffInMinutes(now()) / 60);
            $rate  = $sub->plan->hourlyRate();

            if ($hours > 0 && $rate > 0) {
                $affordable = (int) floor(((float) $company->balance) / $rate);
                $paidHours  = min($hours, $affordable);

                if ($paidHours > 0) {
                    $amount = round($paidHours * $rate, 2);
                    $company->balance = round((float) $company->balance - $amount, 2);
                    $company->save();
                    $sub->last_charged_at = $from->copy()->addHours($paidHours);
                    self::log($company, 'charge', $amount, "{$sub->plan->name} plan · {$paidHours} h");
                }
                if ($paidHours < $hours) {
                    $sub->status = 'expired';
                    $sub->ends_at = $sub->last_charged_at ?? $from;
                }
            }

            $sub->save();
            $company->unsetRelation('current_subscription');

            return $sub;
        });
    }

    /** settle(), at most once a minute per company (it runs on admin and website requests). */
    public static function settleSoon(Company $company): ?Subscription
    {
        if (\Illuminate\Support\Facades\Cache::add("billing-settled:{$company->id}", 1, 60)) {
            self::settle($company);
            $company->unsetRelation('current_subscription');
        }

        return $company->currentSubscription();
    }

    /** Add money to the balance; an expired subscription starts running again right away. */
    public static function topUp(Company $company, float $amount, string $note = 'Top-up'): void
    {
        DB::transaction(function () use ($company, $amount, $note) {
            $company = Company::lockForUpdate()->find($company->id);
            $company->balance = round((float) $company->balance + $amount, 2);
            $company->save();
            self::log($company, 'topup', $amount, $note);

            $sub = Subscription::withoutGlobalScope('company')->with('plan')
                ->where('company_id', $company->id)->latest('id')->first();
            if ($sub?->isExpired() && (float) $company->balance >= $sub->plan->hourlyRate()) {
                $sub->update(['status' => 'active', 'last_charged_at' => now(), 'ends_at' => null]);
            }
        });
    }

    /**
     * Choose or change plan. Only a company's very first plan gets the free trial;
     * changing plan settles the old one up to now and bills the new one from now.
     */
    public static function choosePlan(Company $company, Plan $plan): Subscription
    {
        $current = self::settle($company);

        return DB::transaction(function () use ($company, $plan, $current) {
            $hadPlan = Subscription::withoutGlobalScope('company')->where('company_id', $company->id)->exists();
            $trial   = ! $hadPlan && $plan->trial_days > 0;
            // Changing plan during a trial keeps the remaining trial days.
            $keepTrial = $current?->isTrialing();

            $current?->update(['status' => 'cancelled', 'ends_at' => now()]);

            $balance = (float) Company::find($company->id)->balance;

            return Subscription::create([
                'company_id'      => $company->id,
                'plan_id'         => $plan->id,
                'status'          => $trial || $keepTrial ? 'trialing' : ($balance >= $plan->hourlyRate() ? 'active' : 'expired'),
                'trial_ends_at'   => $trial ? now()->addDays($plan->trial_days) : ($keepTrial ? $current->trial_ends_at : null),
                'starts_at'       => now(),
                'last_charged_at' => $trial || $keepTrial ? null : now(),
            ]);
        });
    }

    /** About how long the balance lasts on this plan (null = no hourly cost). */
    public static function runwayDays(Company $company, ?Plan $plan): ?float
    {
        $rate = $plan?->hourlyRate();

        return $rate > 0 ? ((float) $company->balance) / $rate / 24 : null;
    }

    private static function log(Company $company, string $type, float $amount, string $note): void
    {
        WalletTransaction::withoutGlobalScopes()->create([
            'company_id'    => $company->id,
            'type'          => $type,
            'amount'        => $amount,
            'balance_after' => $company->balance,
            'note'          => $note,
        ]);
    }
}
