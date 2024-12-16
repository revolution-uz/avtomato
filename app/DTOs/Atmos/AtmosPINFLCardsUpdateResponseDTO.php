<?php

namespace App\DTOs\Atmos;

use App\Enums\Company\AtmosPinflPaymentCode;
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
        return (int) $this->code == AtmosPinflPaymentCode::SUCCESS->value;
    }
}
