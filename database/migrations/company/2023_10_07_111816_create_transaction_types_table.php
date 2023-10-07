<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id()->startingValue(100);
            $table->string('name')->nullable();
            $table->string('name_uz');
            $table->string('name_ru');
            $table->string('name_en');
            $table->boolean('is_system');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
};
