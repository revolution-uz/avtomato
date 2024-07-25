<?php

namespace App\DTOs\Atmos\Transction;

use Spatie\LaravelData\Data;

class AtmosTransactionCreateResponseDTO extends Data
{
    public function __construct(
        public string $transaction_status,
        public string $transaction_id,
        public string $ext_id,
        public string $first_name,
        public string $middle_name,
        public string $last_name,
    ) {}

    public function isSucces(): bool
    {
        return $this->transaction_status == 'CREATED';
    }
}
