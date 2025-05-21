<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('installment_id');
            $table->unsignedBigInteger('card_id');
            $table->boolean('auto_charge');
            $table->softDeletes();

            $table->index('installment_id');
            $table->unique(['installment_id', 'card_id']);

            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('card_id')->references('id')->on('cards')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_cards');
    }
};
