<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phoneables', function (Blueprint $table) {
            $table->foreign('phone_id')->cascadeOnDelete();
            $table->foreign('phoneable_id')->restrictOnDelete();
        });
    }
};
