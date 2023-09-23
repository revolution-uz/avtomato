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
        Schema::create('attachmentable', function (Blueprint $table) {
            $table->id();
            $table->uuid('attachment_id');
            $table->unsignedBigInteger('attachmentable_id');
            $table->string('attachmentable_type');
            $table->timestamp('created_at')->nullable();

            $table->index('attachment_id');
            $table->index(['attachmentable_id', 'attachmentable_type']);
            $table->unique(['attachmentable_id', 'attachmentable_type', 'attachment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachmentable');
    }
};
