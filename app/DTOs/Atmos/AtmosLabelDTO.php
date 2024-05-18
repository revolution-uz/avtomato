<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosLabelDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $type_id,
        public ?string $label_ru,
        public ?string $label_uz,
        public ?string $label_en,
    ) {
    }
}
