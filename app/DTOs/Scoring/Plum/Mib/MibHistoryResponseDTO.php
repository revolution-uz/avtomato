<?php

namespace App\DTOs\Scoring\Plum\Mib;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MibHistoryResponseDTO',
    title: 'MibHistoryResponseDTO',
    properties: [
        new OA\Property(property: 'debtBal', type: 'int', example: '1'),
    ],
)]
class MibHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public int $debtBal,
    ) {
    }
}
