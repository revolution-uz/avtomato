<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_delivery_points', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->integer('branch_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_delivery_points', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropColumn('delivery_fee');
            $table->dropColumn('branch_id');
        });
    }
};
