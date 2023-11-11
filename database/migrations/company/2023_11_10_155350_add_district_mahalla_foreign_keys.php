<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->cascadeOnDelete();
        });
        Schema::table('mahallas', function (Blueprint $table) {
            $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
        });
    }
};
