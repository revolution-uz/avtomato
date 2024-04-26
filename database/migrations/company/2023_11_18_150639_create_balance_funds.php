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
            $table->string('phone_number', 16);
            $table->string('external_transaction_id');
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
