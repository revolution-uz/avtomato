<?php

namespace App\DTOs\Sms;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class SmsFlyResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public bool $success,
        public string $reason,
        public int $resultCode
    ) {}
}
