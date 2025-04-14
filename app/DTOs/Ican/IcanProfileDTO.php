<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanProfileDTO extends Data
{
    public function __construct(
        public ?string $first_name,
        public ?string $last_name,
        public ?string $position,
        public ?string $full_name,
    ) {}
}
