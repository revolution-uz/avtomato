<?php

namespace App\Jobs;

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

class ImportInstallmentIntoUniredJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 500;
    const JOB_ID = 'import-installment-into-unired';

    public function __construct(private readonly int $installmentId)
    {
        $this->onQueue(self::JOB_ID);
    }

    /**
     * @throws Exception
     */
    public function handle(UniredService $uniredService): void
    {
        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        $debt_amount = $installment->debtAmount;
        if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->any_card_charge) {
            if ($installment->isAvailableCustomerPinfl()) {
                $uniredService->createOrUpdateInstallment($installment);
            }
            if ($installment->isAvailableGuarantorPinfl()) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge) {
                        $uniredService->createOrUpdateInstallmentGuarantor($guarantor);
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
