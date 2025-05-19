<?php

namespace App\DTOs\LoyaltyProgram;

use Spatie\LaravelData\Data;

class CartDTO extends Data
{
    public function __construct(
        public string $item_code,
        public float $qty
    ) {}
}
