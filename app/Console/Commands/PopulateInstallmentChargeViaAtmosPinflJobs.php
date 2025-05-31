<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Jobs\InstallmentChargeViaAtmosPinfl;
use App\Models\Company;
use App\Models\Company\Installment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateInstallmentChargeViaAtmosPinflJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:installment-charge-via-atmos-pinfl-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate installment charge via Atmos PINFL jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $i = 0;

        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (! Utilities::isAllowedAutoChargeByPinfl(FeatureEnum::ATMOS_PINFL_PAY)) {
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
                InstallmentChargeViaAtmosPinfl::dispatch($installment->id)->delay(now()->addSeconds($i));
                $i++;
            }
        }

        return CommandAlias::SUCCESS;
    }
}
