<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction_types', function (Blueprint $table) {
            // Remove the 'name' column
            $table->dropColumn('name');

            // Modify the 'name_uz', 'name_ru', and 'name_en' columns as required
            $table->string('name_uz');
            $table->string('name_ru');
            $table->string('name_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_types', function (Blueprint $table) {
            // Define the reverse operations in case you need to rollback the migration
            $table->string('name');
            $table->string('name_uz')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
        });
    }
};
