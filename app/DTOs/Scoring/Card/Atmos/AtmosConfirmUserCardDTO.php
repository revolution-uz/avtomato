<?php

namespace App\DTOs\Scoring\Card\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosConfirmUserCardDTO',
    title: 'PlumTech response to create card request',
    properties: [
        new OA\Property(property: 'result', ref: '#/components/schemas/AtmosResultDTO'),
        new OA\Property(property: 'data', ref: '#/components/schemas/AtmosCardDTO'),
    ],
)]
class AtmosConfirmUserCardDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public AtmosCardDTO $data,
    ) {
    }
}
