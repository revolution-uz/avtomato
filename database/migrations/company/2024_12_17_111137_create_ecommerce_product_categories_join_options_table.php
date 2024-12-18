<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_product_categories_join_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('ecommerce_product_categories');
            $table->foreignId('option_id')->constrained('ecommerce_product_options');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_categories_join_options');
    }
};
