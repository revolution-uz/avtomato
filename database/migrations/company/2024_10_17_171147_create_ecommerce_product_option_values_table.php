<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_product_option_values', function (Blueprint $table) {
            $table->id();
            $table->string('value_uz');
            $table->string('value_ru');
            $table->string('value_en');
            $table->string('value_oz');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_option_values');
    }
};
