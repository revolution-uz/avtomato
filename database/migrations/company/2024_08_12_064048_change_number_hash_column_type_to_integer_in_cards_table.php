<?php

use App\Models\Company\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            Card::all()->each(function ($card) {
                $card->number_hash = Card::hash($card->number);
                $card->save();
            });

            DB::statement('ALTER TABLE cards ALTER COLUMN number_hash TYPE INTEGER USING number_hash::integer');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            DB::statement('ALTER TABLE cards ALTER COLUMN number_hash TYPE VARCHAR USING number_hash::varchar');
        });
    }
};
