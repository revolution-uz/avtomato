<?php

namespace App\DTOs\Management;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;

#[OA\Schema(
    schema: 'FundBalanceResponseDTO',
    title: 'FundBalanceResponseDTO',
    properties: [
        new OA\Property(property: 'session', type: 'int', example: '153'),
        new OA\Property(property: 'transactionId', type: 'int', example: '145'),
        new OA\Property(property: 'otpSentPhone', type: 'string', example: '99891*****97'),
    ],
)]
class FundBalanceResponseDTO extends Data
{
    public function __construct(
        public string $session,
        public int $transactionId,
        public string $otpSentPhone
    ) {

    }
}
