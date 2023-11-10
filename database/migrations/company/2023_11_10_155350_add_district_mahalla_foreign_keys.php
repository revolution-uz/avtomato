<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->foreign('region_id')->cascadeOnDelete();
        });
        Schema::create('mahallas', function (Blueprint $table) {
            $table->foreign('district_id')->cascadeOnDelete();
        });
    }
};
