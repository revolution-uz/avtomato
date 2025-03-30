<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone', 16);
            $table->boolean('is_checked');
            $table->string('comment', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_customers');
    }
};
