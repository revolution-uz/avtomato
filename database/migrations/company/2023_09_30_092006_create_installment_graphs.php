<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_graphs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installment_id');
            $table->decimal('amount', 10);
            $table->decimal('paid_amount', 10);
            $table->integer('overdue_days')->default(0);
            $table->integer('status');
            $table->integer('starts_in')->nullable();
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            //indexes
            $table->index('installment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_graphs');
    }
};
