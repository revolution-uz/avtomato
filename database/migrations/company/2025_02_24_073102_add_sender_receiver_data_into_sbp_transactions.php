<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sbp_transactions', function (Blueprint $table) {
            $table->json('sender')->nullable()->after('debit');
            $table->json('receiver')->nullable()->after('sender');
        });
    }

    public function down(): void
    {
        Schema::table('sbp_transactions', function (Blueprint $table) {
            $table->dropColumn('sender');
            $table->dropColumn('receiver');
        });
    }
};
