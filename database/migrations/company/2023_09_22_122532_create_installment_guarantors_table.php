<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_guarantors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('installment_id');
            $table->unsignedBigInteger('customer_id');
            $table->boolean('auto_charge');
            $table->boolean('any_card_charge');
            $table->integer('status');
            $table->string('myuzkard_id')->nullable();
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index('installment_id');
            $table->index('customer_id');
            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_guarantors');
    }
};
