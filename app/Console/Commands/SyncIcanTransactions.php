<?php

namespace App\Console\Commands;

use App\Clients\IcanClient;
use App\DTOs\Ican\Request\IcanTransactionRequestDTO;
use App\Models\Company;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncIcanTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-ican-transactions {companyId : The ID of the company to sync transactions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Ican transactions for a specific company';

    /**
     * Execute the console command.
     */
    public function handle(IcanClient $icanClient): int
    {
        tenancy()->initialize(
            Company::query()->findOrFail(
                $this->argument('companyId')
            )
        );

        $transactions = Transaction::query()
            ->where('transaction_type_id', TransactionType::UNIRED_WITH_PINFL)
            ->whereNull('ican_id')
            ->whereDate('created_at', '<=', Carbon::now()->subDays(3))
            ->get();

        foreach ($transactions as $transaction) {
            $dto = IcanTransactionRequestDTO::from([
                'credit_id' => $transaction->installment->ican_id,
                'amount' => $transaction->amount < 1 ? 1 : $transaction->amount,
                'employee_id' => 335514,
            ]);

            $icanTransaction = $icanClient->createTransaction($dto);

            $transaction->ican_id = $icanTransaction->id;
            $transaction->save();

            Log::info('Transaction synced successfully', [
                'transaction_id' => $transaction->id,
                'ican_id' => $icanTransaction->id,
            ]);
        }

        Log::info('Ican transactions sync completed', [
            'total_processed' => $transactions->count(),
        ]);

        return CommandAlias::SUCCESS;
    }
}
