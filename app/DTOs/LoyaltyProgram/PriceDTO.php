<?php

namespace App\DTOs\LoyaltyProgram;

use Spatie\LaravelData\Data;

class PriceDTO extends Data
{
    public function __construct(
        public string $item_code,
        public int $shop_id,
        public float $price
    ) {}
}
