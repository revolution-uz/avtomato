<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id')->index();
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')->onDelete('cascade');
            $table->unsignedBigInteger('feature_id')->index();
            $table->foreign('feature_id')
                ->references('id')
                ->on('features')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_features');
    }
};
