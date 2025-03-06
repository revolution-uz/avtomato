<?php

namespace App\DTOs\LoyaltyProgram;

use Spatie\LaravelData\Data;

class ItemDTO extends Data
{
    public function __construct(
        public string $code,
        public ?string $art,
        public ?string $name,
        public ?string $mxik,
        public ?string $packageCode,
        public ?string $gtin
    ) {}
}
