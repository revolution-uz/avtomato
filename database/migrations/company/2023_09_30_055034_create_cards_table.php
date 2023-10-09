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
            $table->unsignedBigInteger('customer_id');
            $table->string('pan');
            $table->string('number');
            $table->string('expiry');
            $table->tinyInteger('status');
            $table->decimal('balance');
            $table->unsignedBigInteger('myuzkard_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
