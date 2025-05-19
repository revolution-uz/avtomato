<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredChangePaymentSendStatusRequestDTO
 *
 * @property array $transaction_ids
 * @property bool $is_sent
 */
class UniredChangePaymentSendStatusRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public array $transaction_ids,
        public bool $is_sent,
    ) {}
}
