<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosTransactionReversePaymentDto extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public ?int $transaction_id
    ) {
    }
}
