<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosCardDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $card_id,
        public ?string $pan,
        public ?string $expiry,
        public ?string $card_holder,
        public ?int $balance, //tiyin
        public ?string $phone,
        public ?string $card_token,
    ) {
    }
}
