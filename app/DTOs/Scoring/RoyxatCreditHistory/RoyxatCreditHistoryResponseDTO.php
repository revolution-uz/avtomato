<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class RoyxatCreditHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public bool $ok,
        public ?RoyxatCreditHistoryResultDTO $result,
        public ?int $error_code,
        public ?string $description
    ) {}

    public function isSuccess(): bool
    {
        return $this->ok === true;
    }
}
