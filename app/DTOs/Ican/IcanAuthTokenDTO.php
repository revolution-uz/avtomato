<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanAuthTokenDTO extends Data
{
    public function __construct(
        public ?string $token,
    ) {}
}
