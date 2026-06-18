<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('bi-stars');
            $table->string('color')->default('#198754');
            $table->decimal('monthly_price', 10, 2);
            $table->unsignedSmallInteger('trial_days')->default(0);
            $table->unsignedInteger('website_limit');
            $table->unsignedInteger('product_limit');
            $table->unsignedInteger('staff_limit');
            $table->unsignedInteger('customer_limit');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
