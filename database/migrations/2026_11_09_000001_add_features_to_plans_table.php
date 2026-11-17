<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Common database (companies, domains, billing, sessions...): the default "mysql" connection.
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // App sections the plan unlocks (Role::PERMISSIONS keys). Null = every section.
            $table->json('features')->nullable()->after('customer_limit');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
