<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            $table->foreignId('installment_id')->nullable()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->cascadeOnDelete();
            $table->tinyInteger('customer_type')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            $table->dropforeign('installment_id');
            $table->dropforeign('customer_id');
            $table->dropColumn('customer_type');
        });
    }
};
