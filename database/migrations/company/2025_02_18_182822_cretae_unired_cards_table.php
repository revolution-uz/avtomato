<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unired_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->index();
            $table->string('pinfl');
            $table->string('uuid');
            $table->boolean('auto')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->boolean('is_blocked')->nullable();
            $table->string('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->string('owner')->nullable();
            $table->string('pan')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unired_cards');
    }
};
