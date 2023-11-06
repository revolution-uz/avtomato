<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateUserCardDTO',
    title: 'PlumTech response to create card request',
    properties: [
        new OA\Property(property: 'session', type: 'int', example: '1'),
        new OA\Property(property: 'otpSentPhone', type: 'string', example: '99890*****01'),
    ],
)]
class CreateUserCardDTO extends BaseScoringRequest
{
    public function __construct(
        public int $session,
        public string $otpSentPhone,
    ) {
    }
}
