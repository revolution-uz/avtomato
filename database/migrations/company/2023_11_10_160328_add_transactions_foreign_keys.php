<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('card_id')->references('id')->on('cards')->restrictOnDelete();
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->restrictOnDelete();
        });
    }
};
