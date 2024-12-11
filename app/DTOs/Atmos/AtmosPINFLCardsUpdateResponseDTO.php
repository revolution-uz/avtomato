<?php

namespace App\DTOs\Atmos;

use Spatie\LaravelData\Data;

class AtmosPINFLCardsUpdateResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $message,
        public ?array $payload,
    ) {}

    public function isSuccess(): bool
    {
        return (int) $this->code == 200;
    }
}
