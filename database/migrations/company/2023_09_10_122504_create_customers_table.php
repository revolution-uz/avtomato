<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('pinfl', 14)->unique()->nullable();
            $table->string('tin', 9)->unique()->nullable();
            $table->string('passport_series', 2)->nullable();
            $table->string('passport_number', 7)->nullable();
            $table->date('passport_given_date')->nullable();
            $table->date('passport_expiration_date')->nullable();
            $table->string('passport_given_place', 128)->nullable();
            $table->string('passport_citizenship', 32)->nullable();
            $table->string('first_name', 32)->nullable();
            $table->string('last_name', 32)->nullable();
            $table->string('middle_name', 32)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 32)->nullable();
            $table->boolean('gender')->nullable();
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->unique(['passport_series', 'passport_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
