<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('image_uz_id')->nullable()->index();
            $table->unsignedBigInteger('image_ru_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_notifications', function (Blueprint $table) {
            $table->dropIndex(['image_uz_id', 'image_ru_id']);
            $table->dropColumn('image_uz_id');
            $table->dropColumn('image_ru_id');
        });
    }
};
