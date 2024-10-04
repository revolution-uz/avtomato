<?php

namespace App\Console\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\InstallmentChargeJob;
use App\Models\Company;
use App\Models\Company\Installment;
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
    public function handle(): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);
            /** @var Installment[] $installments */
            $installments = Installment::query()->select('id')
                ->where('status', InstallmentStatusEnum::ACTIVE)
                ->whereNextPayDate('<=', Carbon::now())
                ->get();

            if (! count($installments)) {
                continue;
            }

            foreach ($installments as $index => $installment) {
                dispatch(new InstallmentChargeJob($installment->id))
                    ->onQueue('installment-charge');
            }
        }

        return CommandAlias::SUCCESS;
    }
}
