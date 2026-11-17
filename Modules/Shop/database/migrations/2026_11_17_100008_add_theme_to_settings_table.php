<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Storefront theme folder (Modules\Shop\Support\Storefront::THEMES); "vegitable" by default. */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('theme', 30)->default('vegitable')->after('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};
