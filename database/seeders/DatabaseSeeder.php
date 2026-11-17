<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Restaurant\Database\Seeders\RestaurantDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RestaurantDatabaseSeeder::class);
    }
}
