<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Product properties (Color, Size, RAM...) offered by the categories they are attached to. */
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('attribute_title');
            $table->string('input_type')->default('text'); // select | color | text | number | email
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
