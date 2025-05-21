<?php

namespace App\DTOs\LoyaltyProgram;

use Spatie\LaravelData\Data;

class ShopDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $name,
    ) {}
}
