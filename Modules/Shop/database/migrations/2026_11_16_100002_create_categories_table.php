<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Product categories, up to three levels (parent_id 0 = root). */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unsignedBigInteger('parent_id')->default(0)->index(); // 0 = root
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
