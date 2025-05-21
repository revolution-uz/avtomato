<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('description')->nullable();
            $table->integer('original_amount');
            $table->boolean('is_default_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_modules');
    }
};
