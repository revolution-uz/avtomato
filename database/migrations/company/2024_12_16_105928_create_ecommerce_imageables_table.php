<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('ecommerce_images')->cascadeOnDelete();
            $table->morphs('imageable');
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_imageables', function (Blueprint $table) {
            Schema::dropIfExists('ecommerce_imageables');
        });
    }
};
