<?php

namespace App\Jobs\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Subscription;
use App\Services\LoyaltyProgram\SubscriptionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SubscriptionChargeJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $subscriptionId)
    {
        $this->onQueue('subscription-charge');
    }

    /**
     * @throws Exception
     */
    public function handle(SubscriptionService $subscriptionService): void
    {
        $subscription = Subscription::query()->findOrFail($this->subscriptionId);

        $subscriptionService->chargeSubscription($subscription);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'subscription-charge-' . tenant('id') . '-' . $this->subscriptionId;
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
