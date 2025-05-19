<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCancelPaymentRequestDTO
 *
 * @property string $ext
 */
class UniredCancelPaymentRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext,
    ) {}
}
