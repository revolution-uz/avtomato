<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_product_variations', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('actual_price', 10, 2)->default(0);
            $table->decimal('old_price', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_product_variations', function (Blueprint $table) {
            $table->dropColumn('cost', 'actual_price', 'old_price');
        });
    }
};
