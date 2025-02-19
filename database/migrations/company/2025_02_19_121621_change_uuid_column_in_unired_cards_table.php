<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unired_cards', function (Blueprint $table) {
            $table->string('uuid')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('unired_cards', function (Blueprint $table) {
            $table->string('uuid')->change();
            $table->dropUnique(['uuid']);
        });
    }
};
