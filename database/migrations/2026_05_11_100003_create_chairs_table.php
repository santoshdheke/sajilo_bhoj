<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->enum('side', ['top', 'bottom', 'left', 'right']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chairs');
    }
};
