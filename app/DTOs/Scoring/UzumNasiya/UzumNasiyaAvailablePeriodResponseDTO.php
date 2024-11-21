<?php

namespace App\DTOs\Scoring\UzumNasiya;

use App\DTOs\Scoring\Base\BaseScoringResponse;

/**
 * @property string|null $period
 * @property string|null $title_uz
 * @property string|null $title_ru
 * @property string|null $available_balance
 */
class UzumNasiyaAvailablePeriodResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $period,
        public ?string $title_uz,
        public ?string $title_ru,
        public ?string $available_balance
    ) {}
}
