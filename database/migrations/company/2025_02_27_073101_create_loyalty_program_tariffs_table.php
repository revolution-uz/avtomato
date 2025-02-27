<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name_uz');
            $table->string('name_ru');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('free_months');
            $table->unsignedInteger('subscription_months');
            $table->boolean('is_free_for_previous_subscribers');
            $table->unsignedInteger('limit_amount');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_tariffs');
    }
};
