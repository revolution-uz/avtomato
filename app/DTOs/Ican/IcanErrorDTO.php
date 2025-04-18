<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanErrorDTO extends Data
{
    public function __construct(
        public ?string $errorCode,
        public ?string $errorMessage,
    ) {}
}
