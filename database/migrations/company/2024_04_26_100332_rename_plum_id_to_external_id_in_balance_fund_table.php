<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->renameColumn('plum_transaction_id', 'external_transaction_id');
            $table->string('external_transaction_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->renameColumn('external_transaction_id', 'plum_transaction_id');
            $table->bigInteger('external_transaction_id')->change();
        });
    }
};
