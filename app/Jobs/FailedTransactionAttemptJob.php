<?php

namespace App\Jobs;

use App\Models\Company\TransactionAttempt;
use App\Services\AtmosService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FailedTransactionAttemptJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $transactionAttemptId)
    {
        $this->onQueue('failed-transaction-attempt');
    }

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
        /** @var TransactionAttempt $transactionAttempt */
        $transactionAttempt = TransactionAttempt::query()->findOrFail($this->transactionAttemptId);
        $atmosService->checkIsSuccess($transactionAttempt);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'failed-transaction-attempt-' . tenant('id') . '-' . $this->transactionAttemptId;
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
