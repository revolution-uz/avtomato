<?php

namespace App\DTOs\Atmos\Transction;

use Spatie\LaravelData\Data;

class AtmosTransactionCancelPayloadDTO extends Data
{
    public function __construct(
        public int $transaction_id,
        public int $ext_id,
        public string $transaction_status,
    ) {}
}
