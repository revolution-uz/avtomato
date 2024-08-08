<?php

namespace App\DTOs\Atmos\Transaction;

use Spatie\LaravelData\Data;

class AtmosTransactionInfoResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $description,
        public ?string $message,
        public ?AtmosTransactionInfoPayloadDTO $payload
    ) {}

    public function isSucces(): bool
    {
        return (int) $this->code == 200;
    }
}
