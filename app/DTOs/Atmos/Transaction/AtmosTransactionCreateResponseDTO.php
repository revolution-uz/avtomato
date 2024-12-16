<?php

namespace App\DTOs\Atmos\Transaction;

use App\Enums\Company\AtmosPinflPaymentCode;
use Spatie\LaravelData\Data;

class AtmosTransactionCreateResponseDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $description,
        public ?string $message,
        public ?AtmosTransactionCreatePayloadDTO $payload
    ) {}

    public function isSuccess(): bool
    {
        return (int) $this->code == AtmosPinflPaymentCode::SUCCESS;
    }

    public function isCardNotFound()
    {
        return (int) $this->code == AtmosPinflPaymentCode::CARDS_NOT_FOUND;
    }
}
