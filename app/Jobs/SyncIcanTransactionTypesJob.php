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

class SyncIcanTransactionTypesJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('sync-ican');
    }

    /**
     * @throws Exception
     */
    public function handle(IcanService $icanService): void
    {
        $icanService->syncTransactionTypes();
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'sync-ican-transaction-types-' . tenant('id');
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
