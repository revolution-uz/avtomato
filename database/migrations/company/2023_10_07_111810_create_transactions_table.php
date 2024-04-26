<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->unsignedBigInteger('transaction_type_id');
            $table->string('external_id')->nullable();
            $table->decimal('amount', 12);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('card_id')->references('id')->on('cards')->restrictOnDelete();
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
