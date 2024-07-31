<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosAttachCardResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public ?AtmosResultDTO $result,
        public ?int $transaction_id,
        public ?string $phone,
        public ?AtmosCardDTO $data,
    ) {}
}
