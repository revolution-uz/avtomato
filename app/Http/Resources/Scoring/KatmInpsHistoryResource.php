<?php

namespace App\Http\Resources\Scoring;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmInpsHistoryResource',
    title: 'KatmInpsHistoryResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'status', type: 'int', example: '2'),
        new OA\Property(property: 'request', ref: '#/components/schemas/CreateKatmCreditHistoryRequest'),
        new OA\Property(property: 'response', ref: '#/components/schemas/KatmInpsHistoryResponseDTO', nullable: true),
        new OA\Property(property: 'created_at', type: 'integer', example: 1695643479),
        new OA\Property(property: 'updated_at', type: 'integer', example: 1695643479),
    ],
)]
class KatmInpsHistoryResource extends BaseKatmScoringResource
{
}
