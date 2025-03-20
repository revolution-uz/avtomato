<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['card_id']);
            $table->dropColumn('card_id');
            $table->unsignedBigInteger('remaining_limit_amount');
            $table->timestamp('expired_at');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->index();
            $table->dropColumn('remaining_limit_amount');
            $table->dropColumn('expired_at');
        });
    }
};
