<?php

namespace App\DTOs\Atmos\Transaction;

use App\Enums\Company\AtmosPinflPaymentCode;
use Spatie\LaravelData\Data;

class AtmosTransactionInfoResponseDTO extends Data
{
    public function __construct(
        public ?string $code,
        public ?string $description,
        public ?string $message,
        public ?AtmosTransactionInfoPayloadDTO $payload
    ) {}

    public function isSuccess(): bool
    {
        return (int) $this->code == AtmosPinflPaymentCode::SUCCESS->value;
    }

    public function isReversed(): bool
    {
        return (int) $this->code == AtmosPinflPaymentCode::TRANSACTION_FAILED->value;
    }
}
