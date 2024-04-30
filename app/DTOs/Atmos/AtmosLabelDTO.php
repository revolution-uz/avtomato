<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosLabelDTO',
    title: 'Atmos label DTO',
    properties: [
        new OA\Property(property: 'type_id', type: 'int', example: '1'),
        new OA\Property(property: 'label_ru', type: 'string', example: 'label in ru'),
        new OA\Property(property: 'label_uz', type: 'string', example: 'label in uz'),
        new OA\Property(property: 'label_en', type: 'string', example: 'label in en'),
    ],
)]
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
