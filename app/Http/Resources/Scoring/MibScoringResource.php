<?php

namespace App\Http\Resources\Scoring;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MibScoringResource',
    title: 'MibScoringResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'double', example: '500000'),
        new OA\Property(property: 'ball', type: 'double', example: '1.0'),
        new OA\Property(property: 'description', type: 'string', example: 'Mibdan 500 000 UZS qarzi bor'),
    ],
)]
class MibScoringResource extends BaseKatmScoringResource
{
}
