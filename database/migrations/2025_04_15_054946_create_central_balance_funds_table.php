<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('central_balance_funds', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->string('phone_number', 16)->nullable();
            $table->string('external_transaction_id')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('card_pan')->nullable();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('balance_fund_id')->index();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_balance_funds');
    }
};
