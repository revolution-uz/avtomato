<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosCardDTO',
    title: 'Atmos card response DTO',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
        new OA\Property(property: 'pan', type: 'string', example: '986009******1840'),
        new OA\Property(property: 'expiry', type: 'string', example: '2505'),
        new OA\Property(property: 'card_holder', type: 'string', example: 'TEST'),
        new OA\Property(property: 'balance', type: 'int', example: '1000000'),
        new OA\Property(property: 'phone', type: 'string', example: '998989999999'),
        new OA\Property(property: 'card_token', type: 'string', example: 'card-token'),
    ],
)]
class AtmosCardDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $card_id,
        public ?string $pan,
        public ?string $expiry,
        public ?string $card_holder,
        public ?int $balance, //tiyin
        public ?string $phone,
        public ?string $card_token,
    ) {
    }
}
