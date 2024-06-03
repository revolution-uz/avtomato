<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosPreConfirmPaymentResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
    ) {
    }
}
