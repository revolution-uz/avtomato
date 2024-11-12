<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbp_transactions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column
            $table->string('cheque_ext_id');
            $table->decimal('cheque_amount', 30);
            $table->decimal('cheque_currency', 10);
            $table->string('cheque_partner');
            $table->integer('cheque_terminal_id');
            $table->string('cheque_merchant_id');
            $table->string('cheque_redirect_url');
            $table->string('cheque_purpose_code');
            $table->text('cheque_purpose')->nullable();
            $table->timestamp('cheque_created_at')->nullable();
            $table->timestamp('cheque_updated_at')->nullable();
            $table->decimal('exchange_rate', 10);
            $table->decimal('qr_amount', 30);
            $table->decimal('qr_currency', 10);
            $table->string('qr_qrc_id');
            $table->integer('qr_local_qrc_id');
            $table->boolean('qr_locked')->default(false);
            $table->string('qr_pay_status')->nullable();
            $table->string('qr_pay_trx_id')->nullable();
            $table->string('callback_trx_id')->nullable();
            $table->timestamp('callback_trx_time')->nullable();
            $table->string('callback_snd_pam')->nullable();
            $table->string('callback_snd_phone_masked')->nullable();
            $table->string('callback_add')->nullable();
            $table->string('callback_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbp_transactions');
    }
};
