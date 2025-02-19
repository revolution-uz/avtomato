<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\UpdateCustomerCardsUniredJob;
use App\Models\Company;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateUpdateCustomerCardsUniredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:update-customer-cards-in-unired-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate update customer cards in Unired jobs for customers which has active installments';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        $i = 0;

        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (
                ! Feature::isFeatureActive(FeatureEnum::UNIRED_PAY)
                or ! $balanceService->isAvailableCompany()
            ) {
                continue;
            }

            /** @var Customer[] $customers */
            $customers = Customer::query()
                ->whereHas('installments', fn ($q) => $q
                    ->where('status', InstallmentStatusEnum::ACTIVE))
                ->whereHas('guaranteedInstallments', fn ($q) => $q
                    ->where('status', InstallmentStatusEnum::ACTIVE))
                ->pluck('id');

            foreach ($customers as $customer) {
                UpdateCustomerCardsUniredJob::dispatch($customer->id)->delay(now()->addSeconds($i));
                $i++;
            }
        }

        return CommandAlias::SUCCESS;
    }
}
