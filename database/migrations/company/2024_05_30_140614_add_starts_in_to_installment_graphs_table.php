<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_graphs', function (Blueprint $table) {
            if (! Schema::hasColumn('installment_graphs', 'start_date')) {
                $table->date('starts_in')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('installment_graphs', function (Blueprint $table) {
            if (Schema::hasColumn('installment_graphs', 'start_date')) {
                $table->dropColumn('starts_in');
            }
        });
    }
};
