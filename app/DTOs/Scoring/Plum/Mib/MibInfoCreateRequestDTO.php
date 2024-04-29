<?php

namespace App\DTOs\Scoring\Plum\Mib;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class MibInfoCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $pinfl,
        public string $phone,
    ) {
        if (! str_contains($this->phone, '+')) {
            $this->phone = '+' . $this->phone;
        }
    }
}
