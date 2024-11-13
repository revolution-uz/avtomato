<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\QrPayDTO;

/**
 * Class UniredRegisterPaymentRequestDTO
 *
 * @property string $client_ext_id
 * @property string $payment_ext_id
 * @property string $payment_transaction_id
 * @property int $amount
 */
class UniredRegisterPaymentRequestDTO extends QrPayDTO
{
    public function __construct(
        public string $client_ext_id,
        public string $payment_ext_id,
        public string $payment_transaction_id,
        public int $amount,
    ) {}
}
