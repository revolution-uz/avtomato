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
        Schema::create('phoneables', function (Blueprint $table) {
            $table->id();
            $table->string('comment', 64)->nullable();
            $table->unsignedBigInteger('phone_id');
            $table->unsignedBigInteger('phoneable_id');
            $table->string('phoneable_type', 64); // customer, import_group, etc
            $table->timestamp('created_at')->nullable();

            $table->index('phone_id');
            $table->index(['phoneable_id', 'phoneable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phoneables');
    }
};
