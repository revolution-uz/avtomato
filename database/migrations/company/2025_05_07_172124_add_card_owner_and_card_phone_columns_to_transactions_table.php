<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('card_owner')->nullable()->after('card_pan');
            $table->string('card_phone')->nullable()->after('card_owner');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('card_owner');
            $table->dropColumn('card_phone');
        });
    }
};
