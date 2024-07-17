<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\AtmosStoreTransactionDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosCreatePaymentResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public ?int $transaction_id,
        public ?AtmosStoreTransactionDTO $store_transaction
    ) {}
}
