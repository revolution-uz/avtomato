<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

/**
 * Class CardCreateRequestDTO
 *
 * @property string $number
 * @property string $expiry
 */
class CardCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $number,
        public string $expiry,
    ) {}
}
