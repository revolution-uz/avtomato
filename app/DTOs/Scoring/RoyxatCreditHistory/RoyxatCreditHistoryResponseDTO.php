<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class RoyxatCreditHistoryResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public bool $ok,
        public ?RoyxatCreditHistoryResultDTO $result,
        public ?int $error_code,
        public ?string $description
    ) {}
}
