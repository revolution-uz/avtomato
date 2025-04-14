<?php

namespace App\DTOs\Ican;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class IcanCreditsResponseDTO extends BaseScoringRequest
{
    public function __construct(
        #[DataCollectionOf(IcanCreditDTO::class)]
        public ?DataCollection $data,
        // links,
        public ?IcanMetaDTO $meta,
        // total,
    ) {}
}
