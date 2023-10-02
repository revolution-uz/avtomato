<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->string('address', 128);
            $table->integer('region_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('mahalla_id')->nullable();
            $table->unsignedBigInteger('addressable_id');
            $table->string('addressable_type', 64);
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index('region_id');
            $table->index('district_id');
            $table->index('mahalla_id');
            $table->index(['addressable_id', 'addressable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
