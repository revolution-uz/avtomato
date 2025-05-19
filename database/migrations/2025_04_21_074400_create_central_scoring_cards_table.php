<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_scoring_cards', function (Blueprint $table) {
            $table->id();
            $table->string('number', 1024);
            $table->string('expiry', 4);
            $table->boolean('is_verified')->default(false);
            $table->string('external_id')->nullable();
            $table->unsignedBigInteger('hash');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_scoring_cards');
    }
};
