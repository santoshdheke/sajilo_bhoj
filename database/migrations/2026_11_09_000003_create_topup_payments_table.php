<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per top-up attempt through Khalti / eSewa. Balance is added only when the gateway confirms it.
        Schema::create('topup_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('gateway', ['khalti', 'esewa']);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('reference')->unique();            // our id sent to the gateway (eSewa transaction_uuid / Khalti purchase_order_id)
            $table->string('gateway_token')->nullable();      // Khalti pidx
            $table->string('gateway_txn_id')->nullable();     // gateway's transaction id / ref
            $table->json('response')->nullable();             // last verification response, for support
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_payments');
    }
};
