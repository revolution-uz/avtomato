<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->restrictOnDelete();
            $table->foreign('tariff_id')->references('id')->on('tariffs')->restrictOnDelete();
        });

        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });

        Schema::table('installment_graphs', function (Blueprint $table) {
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
        });

        Schema::table('installment_cards', function (Blueprint $table) {
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('card_id')->references('id')->on('cards')->restrictOnDelete();
        });
    }
};
