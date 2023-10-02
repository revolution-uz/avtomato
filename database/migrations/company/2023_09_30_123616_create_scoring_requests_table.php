<?php

use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scoring_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ext_id')->nullable();
            $table->tinyInteger('status')->default(ScoringRequestStatusEnum::CREATED->value);
            $table->tinyInteger('type');
            $table->tinyInteger('client_code');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->json('request');
            $table->json('response')->nullable();

            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scoring_requests');
    }
};
