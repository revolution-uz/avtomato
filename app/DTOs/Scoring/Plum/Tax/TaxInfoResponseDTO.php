<?php

namespace App\DTOs\Scoring\Plum\Tax;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TaxInfoResponseDTO',
    title: 'TaxInfoResponseDTO',
    properties: [
        new OA\Property(property: 'debtBal', type: 'int', example: '1'),
    ],
)]
class TaxInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public float $debtBal,
    ) {}
}
