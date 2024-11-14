<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_product_join_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('option_id');
            $table->bigInteger('value_id');
            $table->bigInteger('product_id');
            $table->bigInteger('product_variation_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_join_options');
    }
};
