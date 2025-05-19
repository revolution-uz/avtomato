<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tags', function (Blueprint $table) {
            $table->integer('customer_id')->unsigned();
            $table->integer('tag_id')->unsigned();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tags');
    }
};
