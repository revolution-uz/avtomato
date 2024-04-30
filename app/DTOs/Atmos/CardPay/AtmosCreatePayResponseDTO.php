<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\AtmosStoreTransactionDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosCreatePayResponseDTO',
    title: 'AtmosCreatePayResponseDTO',
    properties: [
        new OA\Property(property: 'result', ref: '#/components/schemas/AtmosResultDTO'),
        new OA\Property(property: 'transaction_id', type: 'int', example: '1'),
        new OA\Property(property: 'store_transaction', ref: '#/components/schemas/AtmosStoreTransactionDTO'),
    ],
)]
class AtmosCreatePayResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
        public ?int $transaction_id,
        public AtmosStoreTransactionDTO $store_transaction
    ) {
    }
}
