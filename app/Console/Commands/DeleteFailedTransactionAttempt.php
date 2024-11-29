<?php

namespace App\Console\Commands;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Jobs\DeleteFailedTransactionAttemptJob;
use App\Models\Company;
use App\Models\Company\TransactionAttempt;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DeleteFailedTransactionAttempt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:failed-transaction-attempt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Failed Transaction Attempt';

    /**
     * Execute the console command.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            /** @var TransactionAttempt[] $transactionAttempts */
            $transactionAttempts = TransactionAttempt::query()->select('id')
                ->where('status', TransactionAttemptStatusEnum::SUCCESS)
                ->get();

            if (! count($transactionAttempts)) {
                continue;
            }

            foreach ($transactionAttempts as $transactionAttempt) {
                DeleteFailedTransactionAttemptJob::dispatch($transactionAttempt->id);
            }
        }

        return CommandAlias::SUCCESS;
    }
}
