<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Products. Stock is not stored: purchased quantity minus sold quantity. */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // the app refuses to delete a category that has products; cascade only runs when the whole shop is deleted
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->longText('description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->integer('minimum_stock')->default(5);
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
