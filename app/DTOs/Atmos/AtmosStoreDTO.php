<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosStoreDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $desc,
        public ?string $logo,
        public ?string $ga_id,
    ) {}
}
