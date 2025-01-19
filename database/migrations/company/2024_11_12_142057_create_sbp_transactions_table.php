<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbp_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('terminal_id');
            $table->string('ext_id');
            $table->integer('status');
            $table->decimal('amount', 30);
            $table->json('cheque')->nullable();
            $table->json('exchange')->nullable();
            $table->json('qr')->nullable();
            $table->json('callback')->nullable();
            $table->timestamp('payment_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbp_transactions');
    }
};
