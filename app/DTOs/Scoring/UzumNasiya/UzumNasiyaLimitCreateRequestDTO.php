<?php

namespace App\DTOs\Scoring\UzumNasiya;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class UzumNasiyaLimitCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $phone
    ) {}
}
