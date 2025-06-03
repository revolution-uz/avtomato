<?php

namespace App\Console\Commands;

use App\Enums\FeatureEnum;
use App\Models\CentralCustomer;
use App\Models\Company;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class BalanceFundsUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance-fund:pinfl-cards {company_id : The ID of the company to update balance funds for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates balance fund charges for customers with PINFL cards for a given company.';

    public function handle(): int
    {
        Log::info('BalanceCharge process started');

        tenancy()->initialize(
            Company::query()->findOrFail(
                $this->argument('company_id')
            )
        );

        $createdCount = 0;
        $totalAmount = 0;

        Customer::query()
            ->whereNotNull('pinfl')
            ->chunk(100, function ($customers) use (&$createdCount, &$totalAmount) {
                $centralCustomers = CentralCustomer::query()
                    ->whereIn('pinfl', $customers->pluck('pinfl'))
                    ->where(fn ($q) => $q
                        ->whereNotNull('unired_humo_searched_at')
                        ->orWhereNotNull('unired_uzcard_searched_at'))
                    ->get();

                foreach ($centralCustomers as $centralCustomer) {
                    $feature = Feature::findByName(FeatureEnum::UNIRED_CARD_MANAGEMENT);
                    $customer = $customers->firstWhere('pinfl', $centralCustomer->pinfl);

                    if (
                        BalanceCharge::query()->where(['chargeable_id' => $customer->id, 'chargeable_type' => Customer::class])->doesntExist()
                    ) {
                        $balanceCharge = BalanceCharge::query()->create([
                            'chargeable_id' => $customer->id,
                            'chargeable_type' => Customer::class,
                            'feature_id' => $feature->id,
                            'amount' => $feature->chargeable_amount,
                            'created_at' => $centralCustomer->unired_humo_searched_at ?? $centralCustomer->unired_uzcard_searched_at,
                        ]);
                        $createdCount++;
                        $totalAmount += $feature->chargeable_amount;
                        Log::info('BalanceCharge created', [
                            'customer_id' => $customer->id,
                            'balance_charge_id' => $balanceCharge->id,
                            'amount' => $feature->chargeable_amount,
                        ]);
                    }
                }
            });

        Log::info('BalanceCharge process summary', [
            'created' => $createdCount,
            'total_amount' => $totalAmount,
        ]);

        return CommandAlias::SUCCESS;
    }
}
