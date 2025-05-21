<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active');
            $table->unsignedBigInteger('card_id')->index();
            $table->unsignedBigInteger('tariff_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_subscriptions');
    }
};
