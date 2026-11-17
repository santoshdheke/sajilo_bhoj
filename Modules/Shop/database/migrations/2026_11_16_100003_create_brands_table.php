<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Brands of the products a shop sells. */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('logo')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
