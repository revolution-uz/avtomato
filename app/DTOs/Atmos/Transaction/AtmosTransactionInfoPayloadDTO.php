<?php

namespace App\DTOs\Atmos\Transaction;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AtmosTransactionInfoPayloadDTO extends Data
{
    public function __construct(
        public string $transaction_id,
        public string $ext_id,
        public string $transaction_status,
        #[DataCollectionOf(AtmosTransactionDetailDTO::class)]
        public ?DataCollection $details,
    ) {}

    public function isSucces(): bool
    {
        return $this->transaction_status == 'SUCCESS';
    }
}
