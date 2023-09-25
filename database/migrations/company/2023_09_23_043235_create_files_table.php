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
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('type'); // avatar, document, etc.
            $table->string('path', 128); // uploads/{companyId}/uuid.extension
            $table->string('preview_path', 128); // thumb image of file
            $table->string('name', 128);
            $table->string('original_name', 64);
            $table->string('mime', 64);
            $table->unsignedBigInteger('attachmentable_id')->nullable();
            $table->string('attachmentable_type', 64)->nullable();

            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();

            $table->index(['attachmentable_id', 'attachmentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
