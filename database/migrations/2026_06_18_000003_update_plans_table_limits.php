<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('website_limit');
            $table->renameColumn('product_limit', 'item_limit');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('item_limit')->nullable()->change();
            $table->unsignedInteger('staff_limit')->nullable()->change();
            $table->unsignedInteger('customer_limit')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('item_limit', 'product_limit');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('product_limit')->nullable(false)->change();
            $table->unsignedInteger('staff_limit')->nullable(false)->change();
            $table->unsignedInteger('customer_limit')->nullable(false)->change();
            $table->unsignedInteger('website_limit')->default(1);
        });
    }
};
