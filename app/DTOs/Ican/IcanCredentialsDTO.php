<?php

namespace App\DTOs\Ican;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class IcanCredentialsDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $username,
        public ?string $password,
        public ?int $expires_in,
        public ?string $access_token,
    ) {}
}
