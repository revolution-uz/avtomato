<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_items', function (Blueprint $table) {
            $table->id();
            $table->string('external_code');
            $table->bigInteger('external_shop_id')->nullable();
            $table->string('external_art')->nullable();
            $table->string('external_name')->nullable();
            $table->string('external_mxik')->nullable();
            $table->string('external_package_code')->nullable();
            $table->string('external_gtin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_items');
    }
};
