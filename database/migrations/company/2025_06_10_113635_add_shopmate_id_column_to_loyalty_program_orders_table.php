<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shopmate_id')->index()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_orders', function (Blueprint $table) {
            $table->dropIndex(['shopmate_id']);
            $table->dropColumn('shopmate_id');
        });
    }
};
