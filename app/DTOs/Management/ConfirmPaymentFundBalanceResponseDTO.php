<?php

namespace App\DTOs\Management;

use Spatie\LaravelData\Data;

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
    ) {}
}
