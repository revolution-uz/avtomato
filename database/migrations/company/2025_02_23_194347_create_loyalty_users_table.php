<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_users', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('device_id')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_users');
    }
};
