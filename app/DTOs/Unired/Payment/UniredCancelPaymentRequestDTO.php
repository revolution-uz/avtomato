<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCancelPaymentRequestDTO
 *
 * @property string $ext_id
 * @property string $transaction_id
 */
class UniredCancelPaymentRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext_id,
        public string $transaction_id,
    ) {}
}
