<?php

namespace App\DTOs\Management;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;

#[OA\Schema(
    schema: 'FundBalanceResponseDTO',
    title: 'FundBalanceResponseDTO',
    properties: [
        new OA\Property(property: 'transactionId', type: 'int', example: '148'),
        new OA\Property(property: 'utrno', type: 'string', example: '008493505042'),
        new OA\Property(property: 'responseCode', type: 'int', example: '0'),
        new OA\Property(property: 'terminalId', type: 'string', example: '92403966'),
        new OA\Property(property: 'merchantId', type: 'string', example: '90485446'),
        new OA\Property(property: 'cardNumber', type: 'string', example: '860049******6572'),
        new OA\Property(property: 'date', type: 'string', example: '2020-04-24T15:28:42.442126'),
    ],
)]
class ConfirmPaymentFundBalanceResponseDTO extends Data
{
    public function __construct(
        public int $transactionId,
        public int $responseCode,
        public string $utrno,
        public string $terminalId,
        public string $merchantId,
        public string $cardNumber,
        public string $date,
    ) {

    }
}
