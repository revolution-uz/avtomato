<?php

namespace App\DTOs\Scoring\Card\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosCreateUserCardDTO',
    title: 'PlumTech response to create card request',
    properties: [
        new OA\Property(property: 'result', ref: '#/components/schemas/AtmosResultDTO'),
        new OA\Property(property: 'transaction_id', type: 'int', example: '1'),
        new OA\Property(property: 'phone', type: 'string', example: '+998941234567'),
    ],
)]
class AtmosCreateUserCardDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public int $transaction_id,
        public string $phone,
    ) {
    }
}
