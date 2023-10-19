<?php

namespace App\DTOs\Scoring\Plum\Tax;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class TaxCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $pinfl
    ) {
    }

    public function toArray(): array
    {
        return [
            'pinfl' => $this->pinfl,
        ];
    }
}
