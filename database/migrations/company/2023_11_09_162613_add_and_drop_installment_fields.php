<?php

use App\Enums\Company\InstallmentTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->decimal('initial_fee', 12)->after('amount');
            $table->integer('type')->after('tariff_id')->default(InstallmentTypeEnum::WITHOUT_CARD->value);
        });
    }
};
