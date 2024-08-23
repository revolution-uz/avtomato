<?php

use App\Models\Company\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            Card::query()->withTrashed()->get()->each(function ($card) {
                $card->number_hash = Card::hash($card->number);
                $card->save();
            });
        });
    }
};
