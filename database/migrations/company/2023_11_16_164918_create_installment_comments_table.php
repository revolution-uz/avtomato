<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('installment_id');
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('installment_id');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('installment_id')->references('id')->on('installments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_comments');
    }
};
