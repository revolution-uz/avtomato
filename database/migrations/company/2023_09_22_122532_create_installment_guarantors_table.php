<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_guarantors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_id');
            $table->unsignedBigInteger('customer_id');
            $table->boolean('any_card_charge');
            $table->string('plum_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('installment_id');
            $table->index('customer_id');
            $table->unique(['installment_id', 'customer_id']);

            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_guarantors');
    }
};
