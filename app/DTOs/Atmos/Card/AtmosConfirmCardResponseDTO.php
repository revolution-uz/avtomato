<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosConfirmCardResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public ?AtmosCardDTO $data,
    ) {}
}
