<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_product_option_values', function (Blueprint $table) {
            $table->foreignId('option_id')->nullable()->constrained('ecommerce_product_options');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_product_option_values', function (Blueprint $table) {
            $table->dropForeign(['option_id']);
            $table->dropColumn('option_id');
        });
    }
};
