<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('base_features', function (Blueprint $table) {
            $table->double('humo_percent')->nullable()->after('original_data');
            $table->double('uzcard_percent')->nullable()->after('humo_percent');
        });
    }

    public function down(): void
    {
        Schema::table('base_features', function (Blueprint $table) {
            $table->dropColumn(['humo_percent', 'uzcard_percent']);
        });
    }
};
