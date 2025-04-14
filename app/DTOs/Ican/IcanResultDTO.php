<?php

namespace App\DTOs\Ican;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class IcanResultDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $message,
        public ?array $errors,
    ) {}
}
