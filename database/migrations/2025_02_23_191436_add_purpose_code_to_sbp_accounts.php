<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sbp_accounts', function (Blueprint $table) {
            $table->string('purpose_code')->nullable()->after('inn');
        });
    }

    public function down(): void
    {
        Schema::table('sbp_accounts', function (Blueprint $table) {
            $table->dropColumn('purpose_code');
        });
    }
};
