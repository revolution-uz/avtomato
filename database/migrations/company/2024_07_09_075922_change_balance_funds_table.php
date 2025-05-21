<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->change();
            $table->string('external_transaction_id')->nullable()->change();
            $table->integer('admin_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('balance_funds', function (Blueprint $table) {
            $table->string('phone_number')->nullable(false)->change();
            $table->string('external_transaction_id')->nullable(false)->change();
            $table->dropColumn('admin_id');
        });
    }
};
