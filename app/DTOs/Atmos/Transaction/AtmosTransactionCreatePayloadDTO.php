<?php

namespace App\DTOs\Atmos\Transaction;

use Spatie\LaravelData\Data;

class AtmosTransactionCreatePayloadDTO extends Data
{
    public function __construct(
        public string $transaction_status,
        public string $transaction_id,
        public string $ext_id,
        public string $first_name,
        public string $middle_name,
        public string $last_name,
    ) {}

    public function isSuccess(): bool
    {
        return $this->transaction_status == 'CREATED';
    }
}
