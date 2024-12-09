<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_banners', function (Blueprint $table) {
            $table->foreignId('image_uz_id')->nullable()->constrained('ecommerce_images', 'id');
            $table->foreignId('image_ru_id')->nullable()->constrained('ecommerce_images', 'id');
            $table->foreignId('image_en_id')->nullable()->constrained('ecommerce_images', 'id');
            $table->foreignId('image_oz_id')->nullable()->constrained('ecommerce_images', 'id');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_banners', function (Blueprint $table) {
            $table->dropForeign(['image_uz_id']);
            $table->dropForeign(['image_ru_id']);
            $table->dropForeign(['image_en_id']);
            $table->dropForeign(['image_oz_id']);
        });
    }
};
