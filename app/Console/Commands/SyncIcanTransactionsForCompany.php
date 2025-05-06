<?php

namespace App\Console\Commands;

use App\Clients\IcanClient;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Services\IcanService;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncIcanTransactionsForCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ican-transactions-for-company {--companyId= : The ID of the company to sync transactions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all ICan transactions for given company';

    /**
     * Execute the console command.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        $companyId = $this->option('companyId');
        if (!$companyId) {
            $this->error('Company ID is required.');
            return CommandAlias::FAILURE;
        }

        $this->info('Syncing transactions for company ID: ' . $companyId);

        $company = Company::find($companyId);
        if (!$company) {
            $this->error('Company not found.');
            return CommandAlias::FAILURE;
        }

        if ($company->status !== CompanyStatusEnum::ACTIVE) {
            $this->error('Company is not active.');
            return CommandAlias::FAILURE;
        }

        // Initialize the tenant
        tenancy()->initialize($company);
        // Check if the company has the ICan feature enabled
        if (!Company\Feature::isFeatureActive(FeatureEnum::ICAN)) {
            $this->error('Company does not have the ICan feature enabled.');
            return CommandAlias::FAILURE;
        }

        $icanService = new IcanService(new IcanClient());
        $page = 0;

        do {
            $this->info('Syncing page ' . $page);
            $hasMorePages = $icanService->syncTransactions($page, all: true);
            $page++;
            sleep(1);
        } while ($hasMorePages);

        return CommandAlias::SUCCESS;
    }
}
