<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type'); // avatar, document, etc.
            $table->string('path', 32); // uploads/{companyId}/uuid.extension
            $table->string('preview_path', 32); // thumb image of file
            $table->string('name', 64)->nullable();
            $table->string('original_name', 64);
            $table->string('mime', 64);
            $table->unsignedBigInteger('attachmentable_id');
            $table->string('attachmentable_type', 64);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['attachmentable_id', 'attachmentable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
