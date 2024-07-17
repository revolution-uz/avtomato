<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->double('percent')->nullable();
            $table->double('paid_amount')->default(0);
        });
        DB::unprepared('update installments set percent = (select percent from tariffs where tariffs.id = installments.tariff_id)');
        Schema::table('installments', function (Blueprint $table) {
            $table->double('percent')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('percent');
            $table->dropColumn('paid_amount');
        });
    }
};
