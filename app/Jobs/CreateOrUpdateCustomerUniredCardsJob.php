<?php

namespace App\Jobs;

use App\Enums\FeatureEnum;
use App\Exceptions\UniredException;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Services\UniredService;
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

class CreateOrUpdateCustomerUniredCardsJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const JOB_ID = 'create-or-update-customer-unired-cards-job';

    public function __construct(private readonly int $customerId)
    {
        $this->onQueue(self::JOB_ID);
    }

    /**
     * @throws Exception
     */
    public function handle(UniredService $uniredService, BalanceService $balanceService): void
    {
        if (
            ! Feature::isFeatureActive(FeatureEnum::UNIRED_PAY)
            or ! $balanceService->isAvailableCompany()
        ) {
            return;
        }

        /** @var Customer $customer */
        $customer = Customer::query()->findOrFail($this->customerId);

        try {
            $cards = $uniredService->getCards($customer);

            $centralCustomer = $customer->getOrNewCentralCustomer();

            $centralCustomer->uniredCards()->upsert(
                $cards,
                ['uuid'],
                ['pinfl', 'auto', 'is_verified', 'is_blocked', 'block_reason', 'blocked_at', 'owner', 'pan', 'phone', 'type']
            );

        } catch (UniredException $e) {
            return;
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return self::JOB_ID . '-' . tenant('id') . '-' . $this->customerId;
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
