<?php

namespace App\Http\Resources\Scoring;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TaxInfoResource',
    title: 'TaxInfoResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'status', type: 'int', example: '2'),
        new OA\Property(property: 'request', ref: '#/components/schemas/CreateTaxInfoRequest'),
        new OA\Property(property: 'response', ref: '#/components/schemas/TaxInfoResponseDTO', nullable: true),
        new OA\Property(property: 'created_at', type: 'integer', example: 1695643479),
        new OA\Property(property: 'updated_at', type: 'integer', example: 1695643479),
    ],
)]
class TaxInfoResource extends BaseKatmScoringResource
{
}
