<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_uz');
            $table->string('title_en');
            $table->string('title_ru');
            $table->text('body_uz');
            $table->text('body_en');
            $table->text('body_ru');
            $table->string('image_en');
            $table->string('image_uz')->nullable();
            $table->string('image_ru')->nullable();
            $table->json('meta_description');
            $table->json('meta_keywords');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
