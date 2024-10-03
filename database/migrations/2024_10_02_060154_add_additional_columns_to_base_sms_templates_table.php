<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('base_sms_templates', function (Blueprint $table) {
            $table->json('original_data', 500)->nullable();
            $table->string('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('base_sms_templates', function (Blueprint $table) {
            $table->dropColumn('original_data');
            $table->dropColumn('description');
        });
    }
};
