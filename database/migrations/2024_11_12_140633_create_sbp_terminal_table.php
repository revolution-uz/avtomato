<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbp_terminals', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('terminal_id');
            $table->string('account', 20);
            $table->string('filial_code', 8);
            $table->string('inn', 9);
            $table->string('name', 64);
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbp_terminal');
    }
};
