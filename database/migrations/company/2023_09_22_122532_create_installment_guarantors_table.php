<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installment_guarantors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agreement_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->boolean('autoCharge');
            $table->boolean('anyCardCharge');
            $table->string('status');
            $table->timestamps();

            //foreign keys
            $table->foreign('agreement_id')->references('id')->on('installments');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_guarantors');
    }
};
