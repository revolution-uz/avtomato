<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('region_id')->cascadeOnDelete();
            $table->foreign('district_id')->cascadeOnDelete();
            $table->foreign('mahalla_id')->cascadeOnDelete();
            $table->foreign('addressable_id')->restrictOnDelete();
        });
    }
};
