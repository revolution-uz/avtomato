<?php

namespace App\Http\Resources\Scoring;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardScoringResource',
    title: 'CardScoringResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'status', type: 'int', example: '2'),
        new OA\Property(property: 'request', ref: '#/components/schemas/CreateCardScoringRequest'),
        new OA\Property(property: 'response', ref: '#/components/schemas/CardScoringResponseDTO', nullable: true),
        new OA\Property(property: 'created_at', type: 'integer', example: 1695643479),
        new OA\Property(property: 'updated_at', type: 'integer', example: 1695643479),
    ],
)]
class CardScoringResource extends BaseKatmScoringResource
{
}
