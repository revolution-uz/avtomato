<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_users', function (Blueprint $table) {
            $table->unsignedBigInteger('distributor_id')->index()->nullable();
            $table->timestamp('distributed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_users', function (Blueprint $table) {
            $table->dropIndex(['distributor_id']);
            $table->dropColumn('distributor_id', 'distributed_at');
        });
    }
};
