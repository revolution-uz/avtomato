<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('feature_id')->change();
            $table->foreign('feature_id')->references('id')->on('features')->restrictOnDelete();
        });
    }

    public function down(): void
    {

    }
};
