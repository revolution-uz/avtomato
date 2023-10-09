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
            $table->boolean('auto_charge');
            $table->boolean('any_card_charge');
            $table->boolean('is_blacklist');
            $table->tinyInteger('status');
            $table->integer('period');
            $table->string('myuzkard_id')->nullable();
            $table->timestamp('starts_in')->nullable();
            $table->timestamp('next_pay_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('tariff_id');
            $table->index('next_pay_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
