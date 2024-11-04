<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\InstallmentChargeJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateInstallmentChargeJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:installment-charge-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate installment charge jobs for all active installments';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (
                ! (
                    Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->is_active
                    or Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->is_active
                )
                or ! $balanceService->isAvaiblableCompany()
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
                InstallmentChargeJob::dispatch($installment->id);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
