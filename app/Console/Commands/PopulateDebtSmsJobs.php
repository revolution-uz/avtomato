<?php

namespace App\Console\Commands;

use App\Jobs\SendDebtSmsJob;
use App\Models\Company;
use App\Models\Company\Installment;
use App\Services\Installment\InstallmentService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateDebtSmsJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:debt-sms-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate debt sms jobs for all active installments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);

            /** @var Installment[] $installments */
            $installments = InstallmentService::getDebtorInstallments();

            $threshold = 5 * 60.0 / (count($installments) ?: 1);
            foreach ($installments as $index => $installment) {
                SendDebtSmsJob::dispatch($installment->id)->delay(intval($threshold * $index))->onQueue('debt-sms');
            }
        }

        return CommandAlias::SUCCESS;
    }
}
