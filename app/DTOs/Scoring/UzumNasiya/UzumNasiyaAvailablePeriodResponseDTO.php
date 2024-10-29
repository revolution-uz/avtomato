<?php

namespace App\DTOs\Scoring\UzumNasiya;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class UzumNasiyaAvailablePeriodResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $period,
        public ?string $title_uz,
        public ?string $title_ru,
        public ?string $available_balance
    ) {}
}
