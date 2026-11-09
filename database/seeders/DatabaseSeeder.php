<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds only system data (subscription plans). No demo companies are created:
     * a restaurant comes to life through the sign-up page (CompanyRegistrationController),
     * which also gives it its default menu, tables, kitchen columns and staff roles.
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        // Gives any existing company without a subscription the Business plan (no-op on a fresh database).
        $this->call(SubscriptionSeeder::class);
    }
}
