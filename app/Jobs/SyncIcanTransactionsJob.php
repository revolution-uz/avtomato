<?php

namespace App\Jobs;

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

class SyncIcanTransactionsJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $page = 0)
    {
        $this->onQueue('sync-ican');
    }

    /**
     * @throws Exception
     */
    public function handle(IcanService $icanService): void
    {
        $hasMorePages = $icanService->syncTransactions($this->page);

        if ($hasMorePages) {
            self::dispatch($this->page + 1);
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'sync-ican-transactions-' . tenant('id') . '-' . $this->page;
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
