<?php

namespace App\Jobs;

use App\Clients\AtmosCardClient;
use App\Models\Company\Installment;
use App\Services\AtmosService;
use Carbon\Carbon;
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

    private readonly AtmosService $atmosService;

    public function __construct(private readonly int $installmentId)
    {
        $this->atmosService = new AtmosService(new AtmosCardClient);
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($this->installmentId);
        if ($installment->debtAmount > 0 && $installment->next_pay_date <= Carbon::now()) {
            if ($installment->isAvailableCustomerCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id == $installment->customer_id && $installment->debtAmount > 0) {
                        $this->atmosService->withdrawAmountAndCreateCardTransaction($installment, $card, $installment->debtAmount);
                        $installment->refresh();
                    }
                }
            }

            if ($installment->any_card_charge && $installment->debtAmount > 0 && $installment->isAvailableCustomerPinfl()) {
                $this->atmosService->withdrawAmountAndCreatePINFLTransaction($installment, $installment->customer, $installment->debtAmount);
                $installment->refresh();
            }

            if ($installment->debtAmount > 0 && $installment->isAvailableGurantorCard()) {
                foreach ($installment->cards as $card) {
                    if ($card->customer_id != $installment->customer_id && $installment->debtAmount > 0) {
                        $this->atmosService->withdrawAmountAndCreateCardTransaction($installment, $card, $installment->debtAmount);
                        $installment->refresh();
                    }
                }
            }

            if ($installment->debtAmount > 0 && $installment->isAvailableGuarantorPinfl()) {
                foreach ($installment->guarantorList as $guarantor) {
                    if ($guarantor->any_card_charge && $installment->debtAmount > 0) {
                        $this->atmosService->withdrawAmountAndCreatePINFLTransaction($installment, $guarantor->customer, $installment->debtAmount);
                        $installment->refresh();
                    }
                }
            }
        }

        $installment->setNextPayDate();
        $installment->save();
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
