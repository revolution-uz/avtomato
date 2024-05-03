<?php

namespace App\DTOs\Atmos\Transction;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class AtmosTransactionInfoResponseDTO extends Data
{
    public function __construct(
        public int $transaction_id,
        public int $ext_id,
        public string $transaction_status,
        #[DataCollectionOf(AtmosTransactionDetailDTO::class)]
        public ?DataCollection $details,
    ) {
    }
}
