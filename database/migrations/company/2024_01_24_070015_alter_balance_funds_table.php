<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->string('extraId')->after('plum_session');
        });
    }

    public function down(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->dropColumn('extraId');
        });
    }
};
