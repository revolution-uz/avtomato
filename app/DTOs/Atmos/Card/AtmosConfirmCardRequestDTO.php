<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosConfirmCardRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $transaction_id,
        public string $otp,
    ) {
    }
}
