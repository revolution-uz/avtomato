<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            $table->tinyInteger('delivery_type');
            $table->tinyInteger('payment_method');
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->integer('address_id')->nullable()->index()->change();
            $table->integer('delivery_point_id')->nullable()->index()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            $table->dropIndex(['address_id', 'delivery_point_id']);
            $table->dropColumn('delivery_type', 'payment_method', 'shipping_price');
            $table->integer('address_id')->nullable(false)->change();
        });
    }
};
