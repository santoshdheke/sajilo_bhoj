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
     * Refer & Earn earnings wallet: referral bonuses collect here (not in the plan balance).
     * The company chooses to move them into its plan balance or to withdraw them
     * (paid out by the SajiloBhoj team: php artisan referrals:withdrawals).
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('referral_earnings', 12, 2)->default(0)->after('balance');
        });

        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->index(); // users live in the module database (no cross-database FK)
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['esewa', 'khalti', 'bank']);
            $table->string('account_name');
            $table->string('account_number');                  // eSewa / Khalti ID (phone) or bank account number
            $table->string('bank_name')->nullable();
            $table->enum('status', ['pending', 'paid', 'rejected'])->default('pending');
            $table->string('payout_reference')->nullable();    // transaction id of the payout
            $table->string('admin_note')->nullable();          // e.g. reason for rejecting
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        // Every change to the earnings wallet, for the history on the Refer & Earn page.
        Schema::create('earning_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            // bonus: earned from a client's top-up · to_balance: moved into the plan balance ·
            // withdrawal: requested a payout · refund: a rejected withdrawal came back
            $table->enum('type', ['bonus', 'to_balance', 'withdrawal', 'refund']);
            $table->decimal('amount', 12, 2);                  // always positive
            $table->decimal('earnings_after', 12, 2);
            $table->foreignId('source_company_id')->nullable()->constrained('companies')->nullOnDelete(); // bonus: the client
            $table->foreignId('withdrawal_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });

        // Bonuses paid before this wallet existed went straight into the plan balance:
        // record them as earned and moved to balance, so totals stay right and nothing is counted twice.
        foreach (DB::table('wallet_transactions')->where('type', 'bonus')->orderBy('id')->get() as $tx) {
            $row = ['company_id' => $tx->company_id, 'created_at' => $tx->created_at, 'updated_at' => $tx->created_at];
            DB::table('earning_transactions')->insert($row + [
                'type' => 'bonus', 'amount' => $tx->amount, 'earnings_after' => $tx->amount,
                'source_company_id' => $tx->source_company_id, 'note' => $tx->note,
            ]);
            DB::table('earning_transactions')->insert($row + [
                'type' => 'to_balance', 'amount' => $tx->amount, 'earnings_after' => 0, 'note' => 'Added to plan balance automatically',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('earning_transactions');
        Schema::dropIfExists('withdrawal_requests');
        Schema::table('companies', fn (Blueprint $table) => $table->dropColumn('referral_earnings'));
    }
};
