<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

/**
 * Backfills an active subscription for any company that doesn't have one yet,
 * so demo tenants and pre-existing companies aren't blocked by the
 * EnsureSubscriptionChosen gate.
 */
class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plan = Plan::where('slug', 'business')->first();

        if (! $plan) {
            return;
        }

        Company::all()->each(function (Company $company) use ($plan) {
            $exists = Subscription::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->exists();

            if (! $exists) {
                Subscription::create([
                    'company_id' => $company->id,
                    'plan_id'    => $plan->id,
                    'status'     => 'active',
                    'starts_at'  => now(),
                ]);
            }
        });
    }
}
