<?php

namespace App\DTOs\Atmos\Transction;

use Spatie\LaravelData\Data;

class AtmosTransactionCreateResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $description,
        public ?AtmosTransactionCreatePayloadDTO $payload
    ) {}

    public function isSucces(): bool
    {
        return $this->code == '200';
    }
}
