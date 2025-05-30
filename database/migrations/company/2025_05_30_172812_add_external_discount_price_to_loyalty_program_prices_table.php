<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_prices', function (Blueprint $table) {
            $table->decimal('external_discount_price', 20)->nullable()->after('external_price');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_prices', function (Blueprint $table) {
            $table->dropColumn('external_discount_price');
        });
    }
};
