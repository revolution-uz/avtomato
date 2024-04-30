<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosConfirmCardRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public int $transaction_id,
        public string $otp,
    ) {
    }
}
