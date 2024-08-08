<?php

namespace App\DTOs\Atmos\Transaction;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class AtmosTransactionCreatePayloadDTO extends Data
{
    public function __construct(
        public string $transaction_status,
        public string $transaction_id,
        public string $ext_id,
        public int $amount,
        public string $first_name,
        public string $middle_name,
        public string $last_name,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public Carbon $date_created,
    ) {}

    public function isSuccess(): bool
    {
        return $this->transaction_status == 'CREATED';
    }
}
