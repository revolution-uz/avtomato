<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_shops', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_shops', function (Blueprint $table) {
            $table->dropIndex(['image_id']);
            $table->dropColumn('image_id');
        });
    }
};
