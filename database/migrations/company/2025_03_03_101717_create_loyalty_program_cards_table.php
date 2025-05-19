<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_cards', function (Blueprint $table) {
            $table->id();
            $table->string('pan');
            $table->string('number');
            $table->unsignedBigInteger('hash');
            $table->string('expiry');
            $table->string('owner')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedTinyInteger('status');
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('external_id')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_cards');
    }
};
