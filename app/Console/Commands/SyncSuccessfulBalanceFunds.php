<?php

namespace App\Console\Commands;

use App\Models\CentralBalanceFund;
use App\Models\Company;
use App\Models\Company\BalanceFund;
use Illuminate\Console\Command;
use Log;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncSuccessfulBalanceFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:successful-balance-funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync successful balance funds for all companies';

    /**
     * Execute the console command.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        Log::channel('daily')->info('here: ' . Company::query()->count());

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);

            $existBalanceFunds = CentralBalanceFund::where('company_id', $company->id)->pluck('balance_fund_id');
            $balanceFunds = BalanceFund::query()->whereNotIn('id', $existBalanceFunds)->whereSuccess()->get();

            if ($balanceFunds->isNotEmpty()) {
                $centralBalanceFunds = $balanceFunds->map(fn ($balanceFund) => [
                    'amount' => $balanceFund->amount,
                    'phone_number' => $balanceFund->phone_number,
                    'external_transaction_id' => $balanceFund->external_transaction_id,
                    'terminal_id' => $balanceFund->terminal_id,
                    'merchant_id' => $balanceFund->merchant_id,
                    'card_pan' => $balanceFund->card_pan,
                    'company_id' => $company->id,
                    'balance_fund_id' => $balanceFund->id,
                    'created_at' => $balanceFund->created_at,
                ])->toArray();

                CentralBalanceFund::insert($centralBalanceFunds);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
