<?php

namespace App\Console\Commands;

use App\Enums\FeatureEnum;
use App\Jobs\SyncIcanCreditsJob;
use App\Jobs\SyncIcanTransactionsJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateSyncIcanJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:sync-ican-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate ICAN jobs to sync ICAN credits and transactions for all active companies with the ICAN feature enabled';

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

            SyncIcanCreditsJob::dispatch();
            SyncIcanTransactionsJob::dispatch();
        }

        return CommandAlias::SUCCESS;
    }
}
