<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feature_dependencies', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_feature_id');
            $table->unsignedBigInteger('child_feature_id');

            $table->foreign('parent_feature_id')->references('id')->on('features');
            $table->foreign('child_feature_id')->references('id')->on('features');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('');
    }
};
