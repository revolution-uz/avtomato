<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_confirmation_sms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone', 17);
            $table->string('code',6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_attempts');
    }
};
