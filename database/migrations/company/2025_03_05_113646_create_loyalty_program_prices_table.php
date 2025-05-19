<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_prices', function (Blueprint $table) {
            $table->id();
            $table->string('external_item_code');
            $table->unsignedBigInteger('external_shop_id');
            $table->decimal('external_price', 20);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_prices');
    }
};
