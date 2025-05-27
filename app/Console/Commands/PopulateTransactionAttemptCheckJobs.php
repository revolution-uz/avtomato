<?php

namespace App\Console\Commands;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\TransactionAttemptCheckJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\TransactionAttempt;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateTransactionAttemptCheckJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:transaction-attempt-check-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate transaction attempt check jobs for all active installments';

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
                or ! $balanceService->isAvailableCompany()
            ) {
                continue;
            }

            /** @var TransactionAttempt[] $transactionAttempts */
            $transactionAttempts = TransactionAttempt::query()->select('id')
                ->where('status', TransactionAttemptStatusEnum::IN_PROGRESS)
                ->get();

            if (! count($transactionAttempts)) {
                continue;
            }

            foreach ($transactionAttempts as $transactionAttempt) {
                TransactionAttemptCheckJob::dispatch($transactionAttempt->id);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
