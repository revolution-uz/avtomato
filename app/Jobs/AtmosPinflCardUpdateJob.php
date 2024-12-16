<?php

namespace App\Jobs;

use App\Models\Company\Customer;
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

class AtmosPinflCardUpdateJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $customerId)
    {
        $this->onQueue('atmos-pinfl-card-update');
    }

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
        $atmosService->updatePinflCardsList(Customer::findOrFail($this->customerId));
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'atmos-pinfl-card-update-' . tenant('id') . '-' . $this->customerId;
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
