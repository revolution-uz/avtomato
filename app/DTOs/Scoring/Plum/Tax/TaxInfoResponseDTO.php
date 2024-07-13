<?php

namespace App\DTOs\Scoring\Plum\Tax;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class TaxInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public float $debtBal,
    ) {}
}
