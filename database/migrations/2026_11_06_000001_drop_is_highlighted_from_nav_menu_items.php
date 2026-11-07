<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('nav_menu_items', 'is_highlighted')) {
            Schema::table('nav_menu_items', function (Blueprint $table) {
                $table->dropColumn('is_highlighted');
            });
        }
    }

    public function down(): void
    {
        Schema::table('nav_menu_items', function (Blueprint $table) {
            $table->boolean('is_highlighted')->default(false)->after('icon');
        });
    }
};
