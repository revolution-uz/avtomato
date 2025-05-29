<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateUpdateUniredCardJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:update-unired-card-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate update unired card jobs for all active installments';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (
                ! Feature::isFeatureActive(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)
                or ! $balanceService->isAvailableCompany()
                or ! Feature::isFeatureActive(FeatureEnum::UNIRED_PINFL_PAY)
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
