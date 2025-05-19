<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_annual');
            $table->double('percent');
            $table->integer('min_amount');
            $table->integer('max_amount');
            $table->integer('min_initial_fee');
            $table->integer('max_initial_fee');
            $table->integer('min_period');
            $table->integer('max_period');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
