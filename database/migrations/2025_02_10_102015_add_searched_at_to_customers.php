<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('unired_humo_searched_at')->nullable()->after('atmos_pinfl_searched_at');
            $table->timestamp('unired_uzcard_searched_at')->nullable()->after('unired_humo_searched_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['unired_humo_searched_at', 'unired_uzcard_searched_at']);
        });
    }
};
