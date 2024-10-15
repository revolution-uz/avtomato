<?php

namespace App\DTOs\Atmos\Transaction;

use Spatie\LaravelData\Data;

class AtmosTransactionCancelResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $description,
        public ?string $message,
        public ?AtmosTransactionCancelPayloadDTO $payload,
    ) {}

    public function isSuccess(): bool
    {
        return (int) $this->code == 200;
    }
}
