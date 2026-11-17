<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /**
     * Shop owner and staff accounts (guard "shop"). Staff are invited by email and set
     * their own password (invite_token), as in multiple_pasal.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone_number', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('password')->nullable();
            $table->string('role', 20)->default('admin'); // owner | admin (staff)
            $table->boolean('status')->default(true);
            $table->string('invite_token', 80)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['company_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
