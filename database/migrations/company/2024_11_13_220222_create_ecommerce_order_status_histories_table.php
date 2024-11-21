<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_status_id');
            $table->bigInteger('to_status_id');
            $table->bigInteger('admin_id');
            $table->bigInteger('order_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_status_histories');
    }
};
