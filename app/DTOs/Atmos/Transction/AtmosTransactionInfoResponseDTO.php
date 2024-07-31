<?php

namespace App\DTOs\Atmos\Transction;

use Spatie\LaravelData\Data;

class AtmosTransactionInfoResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public string $description,
        public ?AtmosTransactionInfoPayloadDTO $payload
    ) {}

    public function isSucces(): bool
    {
        return $this->code == '200';
    }
}
