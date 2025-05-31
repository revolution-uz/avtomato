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

class InstallmentChargeViaAtmosPinfl implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 500;

    public function __construct(private readonly int $installmentId)
    {
        $this->onQueue('installment-charge-via-atmos-pinfl');
    }

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
        if (! Utilities::isAllowedAutoChargeByPinfl(FeatureEnum::ATMOS_PINFL_PAY)) {
            return;
        }

        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        $debt_amount = $installment->debtAmount;

        if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
            if ($installment->any_card_charge && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->isAvailableCustomerPinfl()) {
                $atmosService->withdrawAmountAndCreatePINFLTransaction($installment->customer, $installment, $debt_amount);
            }

            if ($installment->isAvailableGuarantorPinfl() && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                        $atmosService->withdrawAmountAndCreatePINFLTransaction($guarantor->customer, $installment, $debt_amount);
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
        return 'installment-charge-' . tenant('id') . '-' . $this->installmentId;
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
