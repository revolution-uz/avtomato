<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_dark_mode')->default(false);
            $table->boolean('is_vertical_menu')->default(false);
            $table->boolean('is_bordered_items')->default(true);
            $table->boolean('is_fixed_navbar')->default(false);
            $table->boolean('is_fixed_footer')->default(false);
            $table->boolean('is_fixed_menu')->default(true);
            $table->boolean('is_show_dropdown_on_hover')->default(true);
            $table->string('language')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropColumns('users', [
            'is_dark_mode',
            'is_vertical_menu',
            'is_bordered_items',
            'is_fixed_navbar',
            'is_fixed_footer',
            'is_fixed_menu',
            'is_show_dropdown_on_hover',
            'language',
        ]);
    }
};
