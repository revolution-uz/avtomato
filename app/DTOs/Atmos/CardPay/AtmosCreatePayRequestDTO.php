<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosCreatePayRequestDTO',
    title: 'AtmosCreatePayRequestDTO',
    properties: [
        new OA\Property(property: 'account', type: 'string', example: 'which account'),
        new OA\Property(property: 'amount', type: 'int', example: '100000'),
        new OA\Property(property: 'details', type: 'string', example: 'extra info'),
    ],
)]
class AtmosCreatePayRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $account,
        public string $amount,
        public string $details,
    ) {
    }
}
