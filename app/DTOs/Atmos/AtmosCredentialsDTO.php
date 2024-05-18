<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosCredentialsDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $username,
        public ?string $password,
        public ?int $store_id,
        public ?int $expires_in,
        public ?string $access_token,
        public ?string $token_type = null
    ) {
    }
}
