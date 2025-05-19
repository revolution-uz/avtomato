<?php

namespace App\DTOs\Management;

use Spatie\LaravelData\Data;

class FundBalanceResponseDTO extends Data
{
    public function __construct(
        public string $session,
        public int $transactionId,
        public string $otpSentPhone
    ) {}
}
