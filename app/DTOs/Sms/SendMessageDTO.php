<?php

namespace App\DTOs\Sms;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class SendMessageDTO extends BaseScoringRequest
{
    public function __construct(
        public int $phone,
        public string $message,
    ) {}
}
