<?php

use App\Enums\Company\LoyaltyProgram\ShopStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_program_shops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat')->nullable();
            $table->decimal('lng')->nullable();
            $table->string('image')->nullable();
            $table->smallInteger('status')->default(ShopStatusEnum::WAITING->value);
            $table->string('external_code');
            $table->string('external_name');
            $table->string('external_address');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_shops');
    }
};
