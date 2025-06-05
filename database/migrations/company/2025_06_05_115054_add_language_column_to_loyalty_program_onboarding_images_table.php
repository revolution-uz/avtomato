<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_program_onboarding_images', function (Blueprint $table) {
            $table->renameColumn('image_path', 'image_uz');
            $table->string('image_ru')->nullable()->after('image_uz');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_program_onboarding_images', function (Blueprint $table) {
            $table->renameColumn('image_uz', 'image_path');
            $table->dropColumn('image_ru');
        });
    }
};
