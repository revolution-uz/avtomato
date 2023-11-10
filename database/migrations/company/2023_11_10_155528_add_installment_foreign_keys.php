<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->foreign('customer_id')->cascadeOnDelete();
            $table->foreign('branch_id')->restrictOnDelete();
            $table->foreign('tariff_id')->restrictOnDelete();
        });

        Schema::create('installment_guarantors', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
            $table->foreign('customer_id')->restrictOnDelete();
        });

        Schema::create('installment_graphs', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
        });

        Schema::create('installment_cards', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
            $table->foreign('card_id')->restrictOnDelete();
        });
    }
};
