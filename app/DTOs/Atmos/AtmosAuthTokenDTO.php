<?php

namespace App\DTOs\Atmos;

use Spatie\LaravelData\Data;

class AtmosAuthTokenDTO extends Data
{
    public function __construct(
        public string $access_token,
        public string $scope,
        public string $token_type,
        public int $expires_in
    ) {
    }
}
