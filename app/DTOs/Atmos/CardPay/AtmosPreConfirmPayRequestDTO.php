<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosPreConfirmPayRequestDTO',
    title: 'AtmosPreConfirmPayRequestDTO',
    properties: [
        new OA\Property(property: 'card_token', type: 'string', example: 'card token'),
        new OA\Property(property: 'transaction_id', type: 'int', example: '100000'),
    ],
)]
class AtmosPreConfirmPayRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $card_token,
        public int $transaction_id,
    ) {
    }
}
