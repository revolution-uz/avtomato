<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->foreign('customer_id')->cascadeOnDelete();
            $table->foreign('installment_id')->cascadeOnDelete();
            $table->foreign('card_id')->restrictOnDelete();
            $table->foreign('transaction_type_id')->restrictOnDelete();
        });
    }
};
