<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('brand_id');
            $table->string('name_uz');
            $table->string('name_ru');
            $table->string('name_en');
            $table->string('name_oz');
            $table->string('slug')->unique();
            $table->string('image');
            $table->double('cost');
            $table->double('actual_price');
            $table->double('old_price')->nullable();
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
