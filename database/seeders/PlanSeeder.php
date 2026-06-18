<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter', 'slug' => 'starter', 'icon' => 'bi-flower2', 'color' => '#0e7c4a',
                'monthly_price' => 299, 'trial_days' => 30,
                'item_limit' => 25, 'staff_limit' => 3, 'customer_limit' => 50,
                'sort_order' => 1,
            ],
            [
                'name' => 'Business', 'slug' => 'business', 'icon' => 'bi-rocket-takeoff', 'color' => '#1f5fbf',
                'monthly_price' => 999, 'trial_days' => 30,
                'item_limit' => 100, 'staff_limit' => 10, 'customer_limit' => 200,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional', 'slug' => 'professional', 'icon' => 'bi-gem', 'color' => '#7b1fa2',
                'monthly_price' => 2499, 'trial_days' => 30,
                'item_limit' => 500, 'staff_limit' => 25, 'customer_limit' => 1000,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise', 'slug' => 'enterprise', 'icon' => 'bi-award', 'color' => '#caa325',
                'monthly_price' => 4999, 'trial_days' => 0,
                'item_limit' => null, 'staff_limit' => null, 'customer_limit' => null,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
