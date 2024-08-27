<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable(); // mfo
            $table->string('account_number')->nullable(); // schot
            $table->string('tin')->nullable(); // inn
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_code');
            $table->dropColumn('account_number');
            $table->dropColumn('tin');
        });
    }
};
