<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('tariff_id');
            $table->decimal('amount', 12);
            $table->text('description')->nullable();
            $table->boolean('sms_notify');
            $table->boolean('auto_charge');
            $table->boolean('any_card_charge');
            $table->boolean('is_blacklist');
            $table->string('status');
            $table->string('myuzkard_id')->nullable();
            $table->integer('starts_in')->nullable();
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index('user_id');
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('tariff_id');
            $table->index('card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
