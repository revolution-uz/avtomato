<?php

use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Feature::isFeatureActive(FeatureEnum::ICAN)) {
            $transactionType = TransactionType::getIdByIcanFlag('auto_payment');

            Transaction::query()
                ->whereNotNull('ican_id')
                ->update([
                    'transaction_type_id' => $transactionType,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
