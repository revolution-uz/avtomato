<?php

use App\Models\Company;
use App\Models\Company\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('number_hash')->nullable()->after('number');
        });
        Company::each(function (Company $company) {
            tenancy()->initialize($company);
            Card::each(function (Card $card) {
                $card->update([
                    'number_hash' => Hash::make($card->number),
                ]);
            });
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('number_hash');
        });
    }
};
