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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('tariff_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->boolean('sms_notify');
            $table->boolean('auto_charge');
            $table->boolean('any_card_charge');
            $table->boolean('is_blacklist');
            $table->string('status');
            $table->timestamps();

            //foreign keys
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('tariff_id')->references('id')->on('tariffs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
