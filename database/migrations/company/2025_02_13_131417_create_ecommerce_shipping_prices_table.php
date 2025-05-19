<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_shipping_prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('region_id')->index();
            $table->unsignedBigInteger('district_id')->index();
            $table->unsignedBigInteger('mahalla_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_shipping_prices');
    }
};
