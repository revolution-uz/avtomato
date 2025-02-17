<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12);
            $table->string('card_pan');
            $table->unsignedTinyInteger('status');
            $table->timestamp('paid_at')->nullable();
            $table->string('external_id');
            $table->unsignedInteger('order_id')->index();
            $table->unsignedInteger('transaction_type_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('admin_id')->index()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_payments');
    }
};
