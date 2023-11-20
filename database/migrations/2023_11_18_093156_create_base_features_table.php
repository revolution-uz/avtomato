<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_features', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->index();
            $table->string('description')->nullable();
            $table->boolean('is_default_active');
            $table->integer('original_amount');
            $table->text('original_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_features');
    }
};
