<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmPensionInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public string $result,
        public ?KatmPensionSizePensionDTO $sizePension,
        public string $comments,
    ) {}
}
