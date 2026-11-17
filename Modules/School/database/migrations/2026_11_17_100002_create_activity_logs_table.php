<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'school';

    /** Business/audit trail (Modules\School\Services\ActivityLog\ActivityLogService). */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // No foreign key: audit rows (e.g. a failed login) are kept even if the actor goes away.
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->nullableMorphs('actor');
            $table->string('action')->index();
            $table->nullableMorphs('subject');
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
