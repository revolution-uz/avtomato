<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('region_id');
            $table->integer('oilakredit_id')->unique();
            $table->string('name_oz', 64)->nullable();
            $table->string('name_uz', 64)->nullable();
            $table->string('name_ru', 64)->nullable();
            $table->string('name_en', 64)->nullable();

            $table->index('region_id');
            $table->index('oilakredit_id');

            $table->foreign('region_id')->references('id')->on('regions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
