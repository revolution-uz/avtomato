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
            $table->decimal('amount', 12);
            $table->decimal('paid_amount', 12);
            $table->integer('overdue_days')->default(0);
            $table->tinyInteger('status');
            $table->timestamps();

            //indexes
            $table->index('installment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_graphs');
    }
};
