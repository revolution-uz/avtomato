<?php

namespace App\DTOs\Management;

use Spatie\LaravelData\Data;

class BalanceFundConfirmDTO extends Data
{
    public function __construct(
        public int $transaction_id,
        public int $amount,
        public string $otp,
        public string $number,
        public string $phone,
    ) {}
}
