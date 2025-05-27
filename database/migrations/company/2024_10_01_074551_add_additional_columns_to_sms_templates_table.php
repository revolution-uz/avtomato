<?php

use App\Enums\Company\SmsTemplateStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->tinyInteger('status')->default(SmsTemplateStatusEnum::DRAFT);
            $table->json('data')->nullable();
            $table->string('description')->nullable();
            $table->softDeletes();
        });

        DB::statement('ALTER SEQUENCE sms_templates_id_seq RESTART WITH 100;');
    }

    public function down(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('data');
            $table->dropColumn('description');
            $table->dropSoftDeletes();
        });

        DB::statement('ALTER SEQUENCE sms_templates_id_seq RESTART WITH 1;');
    }
};
