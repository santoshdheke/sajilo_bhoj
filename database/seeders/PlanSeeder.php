<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * Subscription plans built from the app's own sections (Role::PERMISSIONS keys).
 * Each plan includes everything of the plan before it. Dashboard, Users & Roles and
 * Website Settings are in every plan (Plan::CORE), as are the website and online ordering.
 */
class PlanSeeder extends Seeder
{
    /** Run the restaurant floor: take orders, cook them, seat guests. */
    private const STARTER = [
        'dashboard.view', 'orders.manage', 'kitchen.manage', 'tables.manage', 'menu.manage',
        'users.manage', 'settings.manage',
    ];

    /** + get paid and know your guests and numbers. */
    private const BUSINESS = ['billing.view', 'payments.manage', 'customers.manage', 'reports.view'];

    /** + back-of-house: stock, suppliers and purchasing. */
    private const PROFESSIONAL = ['inventory.manage', 'suppliers.manage', 'purchase_orders.manage'];

    public function run(): void
    {
        $business     = [...self::STARTER, ...self::BUSINESS];
        $professional = [...$business, ...self::PROFESSIONAL];

        $plans = [
            [
                'name' => 'Starter', 'slug' => 'starter', 'icon' => 'bi-flower2', 'color' => '#0e7c4a',
                'monthly_price' => 299, 'trial_days' => 30,
                'item_limit' => 25, 'staff_limit' => 3, 'customer_limit' => 50,
                'features' => self::STARTER,
                'sort_order' => 1,
            ],
            [
                'name' => 'Business', 'slug' => 'business', 'icon' => 'bi-rocket-takeoff', 'color' => '#1f5fbf',
                'monthly_price' => 999, 'trial_days' => 30,
                'item_limit' => 100, 'staff_limit' => 10, 'customer_limit' => 200,
                'features' => $business,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional', 'slug' => 'professional', 'icon' => 'bi-gem', 'color' => '#7b1fa2',
                'monthly_price' => 2499, 'trial_days' => 30,
                'item_limit' => 500, 'staff_limit' => 25, 'customer_limit' => 1000,
                'features' => $professional,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise', 'slug' => 'enterprise', 'icon' => 'bi-award', 'color' => '#caa325',
                'monthly_price' => 4999, 'trial_days' => 0,
                'item_limit' => null, 'staff_limit' => null, 'customer_limit' => null,
                'features' => null, // every section, now and in the future
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
