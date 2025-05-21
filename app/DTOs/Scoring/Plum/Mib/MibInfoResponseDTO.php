<?php

namespace App\DTOs\Scoring\Plum\Mib;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class MibInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public int $debtBal,
    ) {}
}
