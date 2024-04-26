<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosStoreDTO',
    title: 'Atmos Store DTO',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'TEST name'),
        new OA\Property(property: 'desc', type: 'string', example: 'TEST desc'),
        new OA\Property(property: 'logo', type: 'string', example: 'TEST logo'),
        new OA\Property(property: 'ga_id', type: 'string', example: 'TEST ga_id'),
    ],
)]
class AtmosStoreDTO extends BaseScoringRequest
{
    public function __construct(
        public int $id,
        public string $name,
        public string $desc,
        public string $logo,
        public string $ga_id,
    ) {
    }
}
