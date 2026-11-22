<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Order form fields the customer fills in when buying a product (colour, RAM, engraving text, delivery date...). */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
