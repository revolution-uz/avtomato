<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_tariffs', function (Blueprint $table) {
            $table->renameColumn('free_months', 'free_days');
            $table->renameColumn('subscription_months', 'subscription_days');
        });

        DB::table('loyalty_program_tariffs')->update([
            'free_days' => DB::raw('free_days * 30'),
            'subscription_days' => DB::raw('subscription_days * 30'),
        ]);
    }

    public function down(): void
    {
        Schema::table('loyalty_program_tariffs', function (Blueprint $table) {
            $table->renameColumn('free_days', 'free_months');
            $table->renameColumn('subscription_days', 'subscription_months');
        });

        DB::table('loyalty_program_tariffs')->update([
            'free_months' => DB::raw('free_months / 30'),
            'subscription_months' => DB::raw('subscription_months / 30'),
        ]);
    }
};
