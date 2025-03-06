<?php

namespace App\Jobs\LoyaltyProgram;

use App\DTOs\LoyaltyProgram\ShopDTO;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Services\LoyaltyProgram\LoyaltyProgramSyncService;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SyncShopsJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const JOB_ID = 'loyalty-program-sync';

    public function __construct(private readonly ShopDTO $shop)
    {
        $this->onQueue(self::JOB_ID);
    }

    /**
     * @throws Exception
     */
    public function handle(LoyaltyProgramSyncService $loyaltyProgramSyncService, BalanceService $balanceService): void
    {
        if (
            ! Feature::isFeatureActive(FeatureEnum::LOYALTY_PROGRAM)
            or ! $balanceService->isAvailableCompany()
        ) {
            return;
        }
        $loyaltyProgramSyncService->createOrUpdateShop($this->shop);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return self::JOB_ID . '-tenant-' . tenant('id') . '-shop-code-' . $this->shop->code;
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
