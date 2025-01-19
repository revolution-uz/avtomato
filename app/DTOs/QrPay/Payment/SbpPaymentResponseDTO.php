<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class SbpPaymentResponseDTO
 *
 * @property SbpPaymentChequeDTO $cheque
 * @property SbpPaymentExchangeDTO $exchange
 * @property SbpPaymentQrDTO $qr
 * @property SbpPaymentCallbackDTO $callback
 * @property Carbon $updated_at
 */
class SbpPaymentResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpPaymentChequeDTO $cheque,
        public ?SbpPaymentExchangeDTO $exchange,
        public ?SbpPaymentQrDTO $qr,
        public ?SbpPaymentCallbackDTO $callback,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i:s', 'Y-m-d H:i:s.u'])]
        public ?Carbon $updated_at,
    ) {}
}
