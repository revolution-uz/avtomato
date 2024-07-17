<?php

namespace App\Jobs;

use App\Models\Company\Installment;
use App\Services\AtmosService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallmentChargeJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 5 * 60;

    public function __construct(private readonly int $installmentId) {}

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        if ($installment->debtAmount > 1) {
            if ($installment->isAvailableCustomerCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id == $installment->customer_id && $installment->debtAmount > 1) {
                        $atmosService->withdrawAmountAndCreateCardTransaction($installment, $card, $installment->debtAmount);
                        $installment->refresh();
                    }
                }
            }

            if ($installment->any_card_charge && $installment->debtAmount > 1 && $installment->isAvailableCustomerPinfl()) {
                $atmosService->withdrawAmountAndCreatePINFLTransaction($installment, $installment->customer, $installment->debtAmount);
                $installment->refresh();
            }

            if ($installment->debtAmount > 1 && $installment->isAvailableGurantorCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id != $installment->customer_id && $installment->debtAmount > 1) {
                        $atmosService->withdrawAmountAndCreateCardTransaction($installment, $card, $installment->debtAmount);
                        $installment->refresh();
                    }
                }
            }

            if ($installment->debtAmount > 1 && $installment->isAvailableGuarantorPinfl()) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge && $installment->debtAmount > 1) {
                        $atmosService->withdrawAmountAndCreatePINFLTransaction($installment, $guarantor->customer, $installment->debtAmount);
                        $installment->refresh();
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
        return 'installment-charge-' . $this->installmentId;
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }
}
