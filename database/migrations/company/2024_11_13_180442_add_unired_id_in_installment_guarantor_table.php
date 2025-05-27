<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->bigInteger('unired_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('installment_guarantors', function (Blueprint $table) {
            $table->dropColumn('unired_id');
        });
    }
};
