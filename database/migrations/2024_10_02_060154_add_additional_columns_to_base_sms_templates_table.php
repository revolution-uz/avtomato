<?php

use App\Enums\Company\SmsTemplateStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('base_sms_templates', function (Blueprint $table) {
            $table->tinyInteger('status')->default(SmsTemplateStatusEnum::DRAFT);
            $table->json('original_data')->nullable();
            $table->string('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('base_sms_templates', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('original_data');
            $table->dropColumn('description');
        });
    }
};
