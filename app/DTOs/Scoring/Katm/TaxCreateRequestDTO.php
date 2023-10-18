<?php

namespace App\DTOs\Scoring\Katm;

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

    public function convertToPlumRequest(): array
    {
        return [
            'pinfl' => $this->pinfl,
        ];
    }
}
