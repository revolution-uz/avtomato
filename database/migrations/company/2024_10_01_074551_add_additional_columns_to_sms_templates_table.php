<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->json('data', 500)->nullable();
            $table->string('description')->nullable();
        });

        DB::statement('ALTER SEQUENCE sms_templates_id_seq RESTART WITH 100;');
    }

    public function down(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn('data');
            $table->dropColumn('description');
        });

        DB::statement('ALTER SEQUENCE sms_templates_id_seq RESTART WITH 1;');
    }
};
