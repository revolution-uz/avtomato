<?php

namespace App\DTOs\Atmos\Card;

use Spatie\LaravelData\Data;

class AtmosInfoCardDTO extends Data
{
    public function __construct(
        public string $id,
        public int $balance,
        public string $expiry,
        public string $full_name,
        public string $pan,
        public string $phone,
        public bool $sms,
        public int $status,
        public string $card_type,
        public ?array $result,
        public int $balance_sum,
    ) {}
}
