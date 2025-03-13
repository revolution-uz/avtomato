<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('loyalty_program_users');
            $table->foreignId('order_id')->constrained('orders');
            $table->bigInteger('admin_id')->nullable();
            $table->smallInteger('status')->default(LoyaltyProgramOrderStatusEnum::PENDING->value);
            $table->smallInteger('discount_type')->default(LoyaltyProgramDiscountTypeEnum::PERCENTAGE->value);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_discount_amount', 10, 2);
            $table->decimal('total_paid_amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loyalty_program_order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('loyalty_program_order');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loyalty_program_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('loyalty_program_order');
            $table->bigInteger('admin_id')->nullable();
            $table->smallInteger('from_status');
            $table->smallInteger('to_status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loyalty_program_order_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('loyalty_program_order');
            $table->bigInteger('admin_id')->nullable();
            $table->smallInteger('payment_method');
            $table->smallInteger('status')->default(LoyaltyProgramOrderPaymentStatusEnum::PENDING->value);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_order');
        Schema::dropIfExists('loyalty_program_order_item');
        Schema::dropIfExists('loyalty_program_order_status_history');
        Schema::dropIfExists('loyalty_program_order_payment');
    }
};
