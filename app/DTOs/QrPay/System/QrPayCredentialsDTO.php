<?php

namespace App\DTOs\QrPay\System;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class QrPayCredentialsDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $username,
        public ?string $password,
        public ?int $expires_in,
        public ?string $access_token_key,
    ) {}
}
