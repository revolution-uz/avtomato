<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_images', function (Blueprint $table) {
            $table->dropColumn('imagable_id');
            $table->dropColumn('imagable_type');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_images', function (Blueprint $table) {
            $table->unsignedBigInteger('imagable_id');
            $table->string('imagable_type', 64);
        });
    }
};
