<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosConfirmPayRequestDTO',
    title: 'AtmosConfirmPayRequestDTO',
    properties: [
        new OA\Property(property: 'transaction_id', type: 'int', example: '100000'),
    ],
)]
class AtmosConfirmPayRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public int $transaction_id,
    ) {
    }
}
