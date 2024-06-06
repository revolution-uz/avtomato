<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('branch_id');
            $table->date('canceled_at')->nullable()->after('user_id');
            $table->text('cancellation_details')->nullable()->after('canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('canceled_at');
            $table->dropColumn('cancellation_details');
        });
    }
};
