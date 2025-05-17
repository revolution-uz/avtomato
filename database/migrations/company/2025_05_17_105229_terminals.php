<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id');
            $table->integer('type');
            $table->text('data')->nullable();
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->index(['branch_id', 'type']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
