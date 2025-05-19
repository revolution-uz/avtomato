<?php

namespace App\DTOs\LoyaltyProgram;

use Spatie\LaravelData\Data;

class PriceDTO extends Data
{
    public function __construct(
        public string $item_code,
        public string $shop_id,
        public float $price
    ) {}
}
