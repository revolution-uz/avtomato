<?php

namespace App\DTOs\Ican;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class IcanSingleCreditResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public ?IcanCreditDTO $data,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
