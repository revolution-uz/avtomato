<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Company\Transaction;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class AddBranchIdToOldAutoPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopay:add-branch_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add branch id to old automatic payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);
            $transactions = Transaction::query()->whereNull('branch_id')->get();
            if (! count($transactions)) {
                continue;
            }
            foreach ($transactions as $transaction) {
                /** @var Transaction $transaction */
                $branchId = $transaction->installment->branch_id;
                $transaction->branch_id = $branchId;
                $transaction->save();
            }
        }

        return CommandAlias::SUCCESS;
    }
}
