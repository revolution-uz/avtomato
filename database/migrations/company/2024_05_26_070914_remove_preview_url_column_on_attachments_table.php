<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            if (Schema::hasColumn('attachments', 'preview_path')) {
                $table->dropColumn('preview_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            if (! Schema::hasColumn('attachments', 'preview_path')) {
                $table->string('preview_path')->nullable();
            }
        });
    }
};
