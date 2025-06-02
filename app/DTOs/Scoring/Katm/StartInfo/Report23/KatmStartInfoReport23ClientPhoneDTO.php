<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23ClientPhoneDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $phone_number,
        public ?string $phone_number_change,
    ) {}
}
