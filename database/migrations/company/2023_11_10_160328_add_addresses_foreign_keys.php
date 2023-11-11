<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->restrictOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->restrictOnDelete();
            $table->foreign('mahalla_id')->references('id')->on('mahallas')->restrictOnDelete();
        });
    }
};
