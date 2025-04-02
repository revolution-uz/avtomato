<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 2, 10);
            $table->unsignedTinyInteger('status');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('card_id')->index();
            $table->string('external_id')->nullable();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_payments');
    }
};
