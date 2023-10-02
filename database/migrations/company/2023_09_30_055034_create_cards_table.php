<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('pan');
            $table->string('number');
            $table->string('expiry');
            $table->integer('status');
            $table->decimal('balance');
            $table->unsignedBigInteger('myuzkard_id');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index('customer_id');
            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
