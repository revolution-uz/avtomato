<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_charges', function (Blueprint $table) {
            $table->id();
            $table->morphs('chargeable');
            $table->integer('amount');
            $table->unsignedBigInteger('feature_id')->index();
            $table->timestamp('created_at')->nullable();

            $table->foreign('feature_id')->references('id')->on('features')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_charges');
    }
};
