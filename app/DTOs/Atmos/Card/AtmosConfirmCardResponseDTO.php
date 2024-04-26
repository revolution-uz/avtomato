<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosConfirmCardResponseDTO',
    title: 'Atmos confirm card response DTO',
    properties: [
        new OA\Property(property: 'result', ref: '#/components/schemas/AtmosResultDTO'),
        new OA\Property(property: 'data', ref: '#/components/schemas/AtmosCardDTO'),
    ],
)]
class AtmosConfirmCardResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public AtmosCardDTO $data,
    ) {
    }
}
