<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateCreateOrUpdateInstallmentInUniredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:create-or-update-installment-in-unired-jobs {--companyId= : The ID of the company to sync transactions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate import installment into Unired jobs for all active installments';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        $companyId = $this->option('companyId');

        foreach (($companyId ? Company::query()->where('id', $companyId)->get() : Company::get()) as $company) {
            tenancy()->initialize($company);

            if (
                ! Feature::isFeatureActive(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)
                or ! $balanceService->isAvailableCompany()
                or ! Feature::isFeatureActive(FeatureEnum::UNIRED_PAY)
            ) {
                continue;
            }

            /** @var Installment[] $installments */
            $installments = Installment::query()->select('id')
                ->where('status', InstallmentStatusEnum::ACTIVE)
                ->whereNextPayDate('<=', Carbon::now())
                ->get();

            if (! count($installments)) {
                continue;
            }

            foreach ($installments as $installment) {
                CreateOrUpdateInstallmentInUniredJob::dispatch($installment->id);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
