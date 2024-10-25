<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_commerce_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('address_id');
            $table->integer('status_id');
            $table->integer('installment_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_commerce_orders');
    }
};
