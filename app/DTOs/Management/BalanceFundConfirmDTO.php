<?php

namespace App\DTOs\Management;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;

#[OA\Schema(
    schema: 'BalanceFundConfirmDTO',
    title: 'BalanceFundConfirmDTO',
    properties: [
        new OA\Property(property: 'transaction_id', type: 'int', example: '153'),
        new OA\Property(property: 'amount', type: 'int', example: '1000000'),
        new OA\Property(property: 'otp', type: 'string', example: '121323'),
        new OA\Property(property: 'number', type: 'string', example: '8600123456784321'),
        new OA\Property(property: 'phone', type: 'string', example: '+998991234567'),
    ],
)]
class BalanceFundConfirmDTO extends Data
{
    public function __construct(
        public int $transaction_id,
        public int $amount,
        public string $otp,
        public string $number,
        public string $phone,
    ) {

    }
}
