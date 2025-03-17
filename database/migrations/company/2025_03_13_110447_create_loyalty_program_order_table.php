<?php

use App\Enums\Company\LoyaltyProgramm\DiscountTypeEnum;
use App\Enums\Company\LoyaltyProgramm\OrderPaymentStatusEnum;
use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('admin_id')->index()->nullable();
            $table->smallInteger('status')->default(OrderStatusEnum::PENDING->value);
            $table->smallInteger('discount_type')->default(DiscountTypeEnum::PERCENTAGE->value);
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
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loyalty_program_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('admin_id')->index()->nullable();
            $table->smallInteger('from_status');
            $table->smallInteger('to_status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loyalty_program_order_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('admin_id')->index()->nullable();
            $table->smallInteger('payment_method');
            $table->smallInteger('status')->default(OrderPaymentStatusEnum::PENDING->value);
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
