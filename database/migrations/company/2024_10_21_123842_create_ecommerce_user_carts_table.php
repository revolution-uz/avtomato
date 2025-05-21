<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_user_carts', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->bigInteger('product_id');
            $table->bigInteger('variation_id')->nullable();
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_user_carts');
    }
};
