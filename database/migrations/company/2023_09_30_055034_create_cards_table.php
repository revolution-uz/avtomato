<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('pan', 16);
            $table->string('number', 1024);
            $table->string('expiry', 4);
            $table->string('phone')->nullable();
            $table->tinyInteger('status');
            $table->tinyInteger('client_code')->default(\App\Enums\ClientCodeEnum::ATMOS_CARD_PAY->value);
            $table->decimal('balance', 12)->default(0);
            $table->string('external_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
