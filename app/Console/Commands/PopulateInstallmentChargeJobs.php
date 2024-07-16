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
            $installments = Installment::query()
                ->where('status', InstallmentStatusEnum::ACTIVE)
                ->where('next_pay_date', '<', Carbon::today())
                ->select('id')
                ->get()
                ->all();

            if (! count($installments)) {
                continue;
            }

            $threshold = 5 * 60.0 / count($installments);
            foreach ($installments as $index => $installment) {
                dispatch(new InstallmentChargeJob($installment->id))
                    ->delay(intval($threshold * $index))
                    ->onQueue('installment-charge');
            }
        }

        return CommandAlias::SUCCESS;
    }
}
