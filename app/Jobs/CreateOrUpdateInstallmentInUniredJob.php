<?php

namespace App\Jobs;

use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Models\Company\Installment;
use App\Services\UniredService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CreateOrUpdateInstallmentInUniredJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 100;
    const MINIMUM_INSTALLMENT_CREATE_AMOUNT = 10000;
    const JOB_ID = 'create-or-update-installment-in-unired';

    public function __construct(private readonly int $installmentId)
    {
        $this->onQueue(self::JOB_ID);
    }

    /**
     * @throws Exception
     */
    public function handle(UniredService $uniredService): void
    {
        if (! Utilities::isAllowedAutoChargeByPinfl(FeatureEnum::UNIRED_PINFL_PAY)) {
            return;
        }
        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        $debt_amount = $installment->debtAmount;
        if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->any_card_charge && $installment->is_active) {
            if ($installment->isAvailableCustomerPinfl()) {
                if ($installment->unired_id) {
                    $uniredService->updateInstallment($installment);
                    sleep(1);
                } elseif ($debt_amount > self::MINIMUM_INSTALLMENT_CREATE_AMOUNT) {
                    $uniredService->createCustomer($installment->customer);
                    sleep(1);
                    $uniredService->createInstallment($installment);
                    sleep(1);
                }
                SearchCustomerCardInUniredJob::dispatch($installment->id);
            }
            if ($installment->isAvailableGuarantorPinfl()) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge) {
                        if ($guarantor->unired_id) {
                            $uniredService->updateInstallmentGuarantor($guarantor);
                            sleep(1);
                        } elseif ($debt_amount > self::MINIMUM_INSTALLMENT_CREATE_AMOUNT) {
                            $uniredService->createCustomer($guarantor->customer);
                            sleep(1);
                            $uniredService->createInstallmentGuarantor($guarantor);
                            sleep(1);
                        }
                    }
                }
                SearchCustomerCardInUniredJob::dispatch($installment->id);
            }
        } elseif ($installment->unired_id) {
            $uniredService->switchOffInstallment($installment);
            sleep(1);
            foreach ($installment->guarantorList as $guarantor) {
                if ($guarantor->unired_id) {
                    $uniredService->switchOffInstallmentGuarantor($guarantor);
                    sleep(1);
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
