<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Common database (companies, domains, billing, sessions...): the default "mysql" connection.
    protected $connection = 'mysql';

    /**
     * Refer & Earn: a company can add client companies. Every confirmed top-up of a client
     * gives the company that added it a bonus (billing.referral_bonus_percent) on its own balance.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('referred_by_company_id')->nullable()->after('company_type')
                ->constrained('companies')->nullOnDelete();
        });

        DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('topup', 'charge', 'bonus') NOT NULL");

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // For a bonus: the client company whose top-up earned it.
            $table->foreignId('source_company_id')->nullable()->after('type')
                ->constrained('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_company_id');
        });

        DB::table('wallet_transactions')->where('type', 'bonus')->update(['type' => 'topup']);
        DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('topup', 'charge') NOT NULL");

        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_company_id');
        });
    }
};
