<?php

use App\Support\Databases;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'shop';

    /** Storefront settings of a shop (one row per shop): branding colours, font, texts, payments, social links. */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained(Databases::common('companies'))->cascadeOnDelete();
            $table->string('logo_color', 6)->default('627aec');     // primary brand colour (hex without #)
            $table->string('logo_color_two', 6)->default('1c252e'); // secondary brand colour
            $table->string('text_color', 6)->default('ffffff');     // text on the primary colour
            $table->string('font_family', 60)->default('Inter');
            $table->string('announcement')->nullable();
            $table->text('about_us')->nullable();
            $table->string('vat_number', 50)->nullable();
            $table->decimal('default_tax_rate', 5, 2)->default(0);
            $table->text('receipt_footer')->nullable();
            $table->string('esewa_id')->nullable();
            $table->string('esewa_name')->nullable();
            $table->string('khalti_id')->nullable();
            $table->string('khalti_name')->nullable();
            $table->text('khalti_secret_key')->nullable(); // stored encrypted
            $table->string('khalti_env', 10)->default('test'); // test | live
            $table->json('social_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
