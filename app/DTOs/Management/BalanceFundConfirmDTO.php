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
        new OA\Property(property: 'otpSentPhone', type: 'string', example: '99891*****97'),
    ],
)]
class BalanceFundConfirmDTO extends Data
{
    public function __construct(
        public int $transaction_id,
        public int $amount,
        public string $otp,
        public string $card_number,
        public string $phone_number,
    ) {

    }
}
