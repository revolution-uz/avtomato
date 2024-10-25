<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_commerce_product_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('name_uz');
            $table->string('name_ru');
            $table->string('name_en');
            $table->string('name_oz');
            $table->string('image');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_commerce_product_categories');
    }
};
