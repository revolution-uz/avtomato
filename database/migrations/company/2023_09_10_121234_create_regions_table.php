<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->integer('oilakredit_id')->unique();
            $table->string('name_oz', 64)->nullable();
            $table->string('name_uz', 64)->nullable();
            $table->string('name_ru', 64)->nullable();
            $table->string('name_en', 64)->nullable();

            $table->index('oilakredit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
