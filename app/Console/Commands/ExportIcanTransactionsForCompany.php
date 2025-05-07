<?php

namespace App\Console\Commands;

use App\Clients\IcanClient;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Services\IcanService;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ExportIcanTransactionsForCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:ican-transactions-for-company {--companyId= : The ID of the company to export transactions for}';

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
        if (! $companyId) {
            $this->error('Company ID is required.');

            return CommandAlias::FAILURE;
        }

        $this->info('Syncing transactions for company ID: ' . $companyId);

        $company = Company::find($companyId);
        if (! $company) {
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
        if (! Company\Feature::isFeatureActive(FeatureEnum::ICAN)) {
            $this->error('Company does not have the ICan feature enabled.');

            return CommandAlias::FAILURE;
        }

        $icanService = new IcanService(new IcanClient);
        $transactions = Company\Transaction::query()->whereNull('ican_id')->where('status', TransactionStatusEnum::SUCCESS)->get();

        foreach ($transactions as $transaction) {
            $icanTransaction = $icanService->createTransaction($transaction);
            $transaction->ican_id = $icanTransaction->id;
            $transaction->save();
            sleep(3);
        }

        return CommandAlias::SUCCESS;
    }
}
