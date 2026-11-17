<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'school';

    /** A school's classes/grades (Modules\School\Models\ClassroomModel). */
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
