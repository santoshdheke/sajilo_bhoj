<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Online shoppers sign in to the storefront with their customer record (guard "shop_customer"). */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('notes');
            $table->rememberToken()->after('password');
            $table->index(['company_id', 'email']);
            $table->index(['company_id', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'email']);
            $table->dropIndex(['company_id', 'phone_number']);
            $table->dropColumn(['password', 'remember_token']);
        });
    }
};
