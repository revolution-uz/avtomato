<?php

namespace App\Console\Commands;

use App\Enums\FeatureEnum;
use App\Jobs\SyncIcanTransactionTypesJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateSyncIcanTransactionTypeJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:sync-ican-transaction-type-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate jobs to sync ICAN transaction types for companies with the ICAN feature enabled';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (
                ! Feature::isFeatureActive(FeatureEnum::ICAN) || ! $balanceService->isAvailableCompany()
            ) {
                continue;
            }

            SyncIcanTransactionTypesJob::dispatch();
        }

        return CommandAlias::SUCCESS;
    }
}
