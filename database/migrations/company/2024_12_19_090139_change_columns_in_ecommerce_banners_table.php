<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_banners', function (Blueprint $table) {
            $table->unsignedBigInteger('order')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_banners', function (Blueprint $table) {
            $table->dropColumn('starts_at', 'ends_at', 'order');
        });
    }
};
