<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->string('owner_phone', 16);
            $table->string('owner_name', 32);
            $table->string('subdomain', 16)->unique();
            $table->string('db_user')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_password')->nullable();
            $table->tinyInteger('status')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
