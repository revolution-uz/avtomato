<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class CreateUserCardDTO extends BaseScoringRequest
{
    public function __construct(
        public int $session,
        public string $otpSentPhone,
    ) {}
}
