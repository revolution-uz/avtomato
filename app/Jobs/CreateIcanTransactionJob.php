<?php

namespace App\Jobs;

use App\Models\Company\Transaction;
use App\Services\IcanService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CreateIcanTransactionJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $transactionId)
    {
        $this->onQueue('ican-transaction');
    }

    /**
     * @throws Exception
     */
    public function handle(IcanService $icanService): void
    {
        $transaction = Transaction::query()->findOrFail($this->transactionId);

        if ($transaction->ican_id) {
            return;
        }

        //        $debtAmount = app(IcanService::class)->getInstallmentDebtAmount($transaction->installment->ican_id);
        //
        //        if ($debtAmount < $transaction->amount) {
        //            $transaction->reverse();
        //
        //            return;
        //        }

        $icanResponse = $icanService->createTransaction($transaction);
        $transaction->ican_id = $icanResponse->id;
        $transaction->save();

        sleep(20);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'create-ican-transaction-' . tenant('id') . '-' . $this->transactionId;
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }

    /**
     * Get the cache driver for the unique job lock.
     */
    public function uniqueVia(): Repository
    {
        return Cache::driver(config('cache.default', 'redis'));
    }
}
