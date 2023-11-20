<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_funds', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->bigInteger('plum_session');
            $table->string('phone_number', 16);
            $table->bigInteger('plum_transaction_id');
            $table->string('plum_utrno')->nullable();
            $table->integer('plum_response_code')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('card_number')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_funds');
    }
};
