<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosAttachCardDTO',
    title: 'AtmosAttachCardDTO',
    properties: [
        new OA\Property(property: 'card_number', type: 'string', example: '986091******1097'),
        new OA\Property(property: 'expiry', type: 'string', example: '2505'),
    ],
)]
class AtmosAttachCardRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $card_number,
        public string $expiry,
    ) {
    }
}
