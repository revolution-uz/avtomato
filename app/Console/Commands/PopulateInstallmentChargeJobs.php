<?php

namespace App\Console\Commands;

use App\Jobs\InstallmentChargeJob;
use App\Models\Company\Installment;
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
        /** @var Installment[] $installments */
        $installments = Installment::query()->select('id')->get()->all();
        $threshold = 5 * 60.0 / count($installments);
        foreach ($installments as $index => $installment) {
            dispatch(new InstallmentChargeJob($installment->id))
                ->delay(intval($threshold * $index))
                ->onQueue('installment-charge');
        }

        return CommandAlias::SUCCESS;
    }
}
