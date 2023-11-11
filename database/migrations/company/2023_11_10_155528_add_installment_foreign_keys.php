<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->foreign('customer_id')->cascadeOnDelete();
            $table->foreign('branch_id')->restrictOnDelete();
            $table->foreign('tariff_id')->restrictOnDelete();
        });

        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
            $table->foreign('customer_id')->restrictOnDelete();
        });

        Schema::table('installment_graphs', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
        });

        Schema::table('installment_cards', function (Blueprint $table) {
            $table->foreign('installment_id')->cascadeOnDelete();
            $table->foreign('card_id')->restrictOnDelete();
        });
    }
};
