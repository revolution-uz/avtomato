<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosResultDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $code,
        public ?string $description,
    ) {}

    public function isSuccess(): bool
    {
        return $this->code == 'OK';
    }
}
