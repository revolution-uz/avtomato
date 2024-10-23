<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_cart', function (Blueprint $table) {
            $table->integer('customer_id');
            $table->integer('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_cart');
    }
};
