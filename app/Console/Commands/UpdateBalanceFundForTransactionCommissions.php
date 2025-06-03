<?php

namespace App\Console\Commands;

use App\Enums\Company\CardTypeEnum;
use App\Models\Company;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Feature;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UpdateBalanceFundForTransactionCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance-fund:commissions {company_id : The ID of the company to update balance funds for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates balance fund charges for transaction commissions for a given company.';

    public function handle(): int
    {
        Log::info('BalanceCharge process started');

        tenancy()->initialize(
            Company::query()->findOrFail(
                $this->argument('company_id')
            )
        );

        $created = $totalAmount = 0;

        Transaction::query()
            ->whereIn('transaction_type_id', [TransactionType::ATMOS_WITH_CARD, TransactionType::ATMOS_WITH_PINFL, TransactionType::UNIRED_WITH_PINFL])
            ->chunk(100, function ($transactions) use (&$created, &$totalAmount) {
                foreach ($transactions as $transaction) {
                    $featureEnum = TransactionType::getFeature($transaction->transaction_type_id);
                    $feature = Feature::findByName($featureEnum);
                    $commission = max(
                        0,
                        CardTypeEnum::getCardType($transaction->card_pan) === CardTypeEnum::HUMO ?
                            $feature->humo_percent :
                            $feature->uzcard_percent
                    );

                    if (
                        BalanceCharge::query()->where(['chargeable_id' => $transaction->id, 'chargeable_type' => Transaction::class])->doesntExist()
                    ) {
                        $amount = $transaction->amount * $commission;
                        $balanceCharge = BalanceCharge::query()->create([
                            'chargeable_id' => $transaction->id,
                            'chargeable_type' => Transaction::class,
                            'feature_id' => $feature->id,
                            'amount' => $amount,
                            'created_at' => $transaction->created_at,
                        ]);
                        $totalAmount += $amount;
                        $created++;
                        Log::info('BalanceCharge created', [
                            'transaction_id' => $transaction->id,
                            'balance_charge_id' => $balanceCharge->id,
                            'amount' => $amount,
                        ]);
                    }
                }
            });

        Log::info('BalanceCharge process summary', [
            'created' => $created,
            'total_amount' => $totalAmount,
        ]);

        return CommandAlias::SUCCESS;
    }
}
