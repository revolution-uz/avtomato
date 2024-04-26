<?php

namespace App\DTOs\Scoring\Card\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosResultDTO',
    title: 'PlumTech response to create card request',
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'OK'),
        new OA\Property(property: 'description', type: 'string', example: 'Everything is ok'),
    ],
)]
class AtmosResultDTO extends BaseScoringRequest
{
    public function __construct(
        public string $code,
        public string $description,
    ) {
    }

    public function isSucces(): bool
    {
        return $this->code == 'OK';
    }
}
