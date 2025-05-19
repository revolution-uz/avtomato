<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanServiceDataDTO extends Data
{
    public function __construct(
        public ?string $pan,
        // error
    ) {}
}
