<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Prepaid balance: after the free trial, the plan's hourly rate is taken from it.
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('status');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('last_charged_at')->nullable()->after('ends_at'); // paid up to this moment
        });

        // Every top-up and charge, for the history shown to the owner.
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['topup', 'charge']);
            $table->decimal('amount', 12, 2);          // always positive
            $table->decimal('balance_after', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn('last_charged_at'));
        Schema::table('companies', fn (Blueprint $table) => $table->dropColumn('balance'));
    }
};
