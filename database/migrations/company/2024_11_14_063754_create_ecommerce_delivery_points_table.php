<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_delivery_points', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('address_id');
            $table->string('name_uz');
            $table->string('name_ru');
            $table->string('name_en');
            $table->string('name_oz');
            $table->string('description_uz');
            $table->string('description_ru');
            $table->string('description_en');
            $table->string('description_oz');
            $table->boolean('is_prepaid');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_delivery_points');
    }
};
