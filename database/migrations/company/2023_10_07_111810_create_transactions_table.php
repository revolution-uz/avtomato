<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->decimal('amount', 10);
            $table->integer('on_created');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
