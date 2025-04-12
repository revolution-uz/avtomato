<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable();
        });

        DB::table('loyalty_program_notifications')
            ->where('is_sent', true)
            ->update(['sent_at' => DB::raw('updated_at')]);

        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->dropColumn('is_sent');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->boolean('is_sent')->default(false);
        });

        DB::table('loyalty_program_notifications')
            ->whereNotNull('sent_at')
            ->update(['is_sent' => true]);

        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->dropColumn('sent_at');
        });
    }
};
