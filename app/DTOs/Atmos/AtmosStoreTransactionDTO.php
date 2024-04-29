<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosStoreTransactionDTO',
    title: 'Atmos Store Transaction DTO',
    properties: [
        new OA\Property(property: 'success_trans_id', type: 'int', example: '1'),
        new OA\Property(property: 'trans_id', type: 'int', example: '1'),
        new OA\Property(property: 'store', ref: '#/components/schemas/AtmosStoreDTO'),
        new OA\Property(property: 'terminal_id', type: 'string', example: 'W232323SD'),
        new OA\Property(property: 'account', type: 'string', example: 'payment for fund balance'),
        new OA\Property(property: 'amount', type: 'int', example: '1'),
        new OA\Property(property: 'confirmed', type: 'bool', example: 'false'),
        new OA\Property(property: 'prepay_time', type: 'int', example: null),
        new OA\Property(property: 'confirm_time', type: 'int', example: null),
        new OA\Property(property: 'label', ref: '#/components/schemas/AtmosLabelDTO'),
        new OA\Property(property: 'details', type: 'string', example: 'OK'),
        new OA\Property(property: 'commission_value', type: 'string', example: null),
        new OA\Property(property: 'commission_type', type: 'string', example: null),
        new OA\Property(property: 'total', type: 'int', example: '1000000'),
        new OA\Property(property: 'card_id', type: 'int', example: null),
        new OA\Property(property: 'status_code', type: 'int', example: null),
        new OA\Property(property: 'status_message', type: 'string', example: null),
    ],
)]
class AtmosStoreTransactionDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $success_trans_id,
        public ?int $trans_id,
        public AtmosStoreDTO $store,
        public ?string $terminal_id,
        public ?string $account,
        public ?int $amount,
        public ?bool $confirmed,
        public ?int $prepay_time,
        public ?int $confirm_time,
        public AtmosLabelDTO $label,
        public ?string $details,
        public ?string $commission_value,
        public ?string $commission_type,
        public ?int $total,
        public ?int $card_id,
        public ?int $status_code,
        public ?string $status_message,
    ) {
    }
}
