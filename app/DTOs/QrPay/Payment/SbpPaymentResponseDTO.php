<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class SbpPaymentResponseDTO
 *
 * @property SbpPaymentCreditDTO $credit
 * @property SbpPaymentDebitDTO $debit
 * @property SbpPaymentCurrencyDTO $currency
 * @property Carbon $updated_at
 */
class SbpPaymentResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpPaymentCreditDTO $credit,
        public ?SbpPaymentDebitDTO $debit,
        public ?SbpPaymentCurrencyDTO $currency,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i:s', 'Y-m-d H:i:s.u'])]
        public ?Carbon $updated_at,
    ) {}
}
