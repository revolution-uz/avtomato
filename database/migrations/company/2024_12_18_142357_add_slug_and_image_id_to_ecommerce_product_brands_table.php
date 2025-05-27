<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_product_brands', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
            $table->foreignId('image_id')->nullable()->constrained('ecommerce_images');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_product_brands', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
        });
    }
};
