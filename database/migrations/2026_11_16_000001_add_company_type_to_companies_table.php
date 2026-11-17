<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Common database (companies, domains, billing, sessions...): the default "mysql" connection.
    protected $connection = 'mysql';

    /**
     * Which module runs this company's workspace: "restaurant" or "shop"
     * (see App\Models\Company::TYPES). Decides which module's routes a domain gets.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_type', 20)->default('restaurant')->after('slug')->index();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('company_type');
        });
    }
};
