<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            if (! Schema::hasColumn('installments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('branch_id');
            }
            if (! Schema::hasColumn('installments', 'canceled_at')) {
                $table->date('canceled_at')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('installments', 'cancellation_details')) {
                $table->text('cancellation_details')->nullable()->after('canceled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            if (Schema::hasColumn('installments', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('installments', 'canceled_at')) {
                $table->dropColumn('canceled_at');
            }
            if (Schema::hasColumn('installments', 'cancellation_details')) {
                $table->dropColumn('cancellation_details');
            }
        });
    }
};
