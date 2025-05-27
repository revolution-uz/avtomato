<?php

namespace App\DTOs\Scoring\Plum\Tax;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class TaxInfoCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $pinfl
    ) {}
}
