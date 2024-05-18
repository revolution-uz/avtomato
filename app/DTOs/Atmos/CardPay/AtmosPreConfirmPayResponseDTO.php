<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosPreConfirmPayResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
    ) {
    }
}
