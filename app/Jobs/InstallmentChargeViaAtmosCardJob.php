<?php

namespace App\Jobs;

use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Models\Company\Installment;
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

class InstallmentChargeViaAtmosCardJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 500;

    public function __construct(private readonly int $installmentId)
    {
        $this->onQueue('installment-charge-via-atmos-card');
    }

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
        if (! Utilities::isAllowedAutoCharge(FeatureEnum::ATMOS_CARD_PAY)) {
            return;
        }

        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        $debt_amount = $installment->debtAmount;

        if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
            if ($installment->isAvailableCustomerCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id == $installment->customer_id && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                        $atmosService->withdrawAmountAndCreateCardTransaction($card, $installment, $debt_amount);
                    }
                }
            }

            if ($installment->isAvailableGuarantorCard() && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id != $installment->customer_id && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                        $atmosService->withdrawAmountAndCreateCardTransaction($card, $installment, $debt_amount);
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
        return 'installment-charge-via-atmos-card-' . tenant('id') . '-' . $this->installmentId;
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
