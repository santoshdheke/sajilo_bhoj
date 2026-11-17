<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Orange (#f97316) is the default theme colour of a new shop. Existing shops keep their colour. */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('logo_color', 6)->default('f97316')->change();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('logo_color', 6)->default('627aec')->change();
        });
    }
};
