<?php

namespace App\Jobs;

use App\Models\Company\Installment;
use App\Services\AtmosService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallmentChargeJob implements ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    const MINIMUM_WITHDRAW_AMOUNT = 500;

    public function __construct(private readonly int $installmentId) {}

    /**
     * @throws Exception
     */
    public function handle(AtmosService $atmosService): void
    {
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

            if ($installment->any_card_charge && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->isAvailableCustomerPinfl()) {
                $atmosService->withdrawAmountAndCreatePINFLTransaction($installment->customer, $installment, $debt_amount);
            }

            if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->isAvailableGuarantorCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id != $installment->customer_id && $debt_amount > self::MINIMUM_WITHDRAW_AMOUNT) {
                        $atmosService->withdrawAmountAndCreateCardTransaction($card, $installment, $debt_amount);
                    }
                }
            }

            if ($debt_amount > self::MINIMUM_WITHDRAW_AMOUNT && $installment->isAvailableGuarantorPinfl()) {
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
}
