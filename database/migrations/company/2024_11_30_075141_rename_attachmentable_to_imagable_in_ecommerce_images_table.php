<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecommerce_images', function (Blueprint $table) {
            $table->renameColumn('attachmentable_id', 'imagable_id');
            $table->renameColumn('attachmentable_type', 'imagable_type');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_images', function (Blueprint $table) {
            $table->renameColumn('imagable_id', 'attachmentable_id');
            $table->renameColumn('imagable_type', 'attachmentable_type');
        });
    }
};
