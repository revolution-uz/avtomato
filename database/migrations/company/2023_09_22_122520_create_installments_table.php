<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('tariff_id')->nullable();
            $table->decimal('amount', 12);
            $table->text('description')->nullable();
            $table->boolean('sms_notify');
            $table->boolean('any_card_charge');
            $table->boolean('is_blacklist')->default(false);
            $table->tinyInteger('status');
            $table->tinyInteger('period');
            $table->string('myuzkard_id')->nullable();
            $table->date('starts_in')->nullable();
            $table->date('next_pay_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('tariff_id');
            $table->index('next_pay_date');

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->restrictOnDelete();
            $table->foreign('tariff_id')->references('id')->on('tariffs')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
