<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_has_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\App\Models\Company\User::class);
            $table->foreignId(\App\Models\Company\Branch::class);
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_branch');
    }
};
