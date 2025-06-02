<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class KatmStartInfoReport23ClientPhonesDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmStartInfoReport23ClientPhoneDTO::class)]
        public ?DataCollection $phones,
    ) {}
}
