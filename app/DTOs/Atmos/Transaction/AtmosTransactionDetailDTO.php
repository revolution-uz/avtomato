<?php

namespace App\DTOs\Atmos\Transaction;

use Spatie\LaravelData\Data;

class AtmosTransactionDetailDTO extends Data
{
    public function __construct(
        public string $pan,
        public int $amount,
        public int $upper_commission,
        public string $status,
    ) {}
}
