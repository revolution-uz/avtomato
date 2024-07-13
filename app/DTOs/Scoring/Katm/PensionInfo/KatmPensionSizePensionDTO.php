<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class KatmPensionSizePensionDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmPensionDataDTO $data,
        #[DataCollectionOf(KatmPensionRowDTO::class)]
        public ?DataCollection $row,
    ) {}
}
