<?php

namespace App\Jobs;

use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
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

class SearchCustomerCardInUniredJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 50000;
    const JOB_ID = 'search-customer-card-in-unired';

    public function __construct(private readonly int $installmentId)
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

        /** @var Installment $installment */
        $installment = Installment::query()->with('customer', 'guarantorList')->findOrFail($this->installmentId);
        $debt_amount = $installment->debtAmount;
        if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->any_card_charge && $installment->is_active) {
            if ($installment->isAvailableCustomerPinfl()) {
                $uniredService->updatePinflCardsList($installment->customer);
                $uniredService->getAndSyncPinflCards($installment->customer);
                $balanceService->chargeAmount(FeatureEnum::UNIRED_CARD_MANAGEMENT, $installment->customer);
            }
            if ($installment->isAvailableGuarantorPinfl()) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge) {
                        $uniredService->updatePinflCardsList($guarantor->customer);
                        $uniredService->getAndSyncPinflCards($guarantor->customer);
                        $balanceService->chargeAmount(FeatureEnum::UNIRED_CARD_MANAGEMENT, $guarantor->customer);
                    }
                }
            }
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return self::JOB_ID . '-' . tenant('id') . '-' . $this->installmentId;
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
