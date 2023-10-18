<?php

namespace App\DTOs\Scoring\Plum\Mib;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class MibCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $pinfl,
        public string $phone,
    ) {
        $this->phone = '+' . $this->phone;
    }

    public function toArray(): array
    {
        return [
            'pinfl' => $this->pinfl,
            'phone' => $this->phone,
        ];
    }
}
