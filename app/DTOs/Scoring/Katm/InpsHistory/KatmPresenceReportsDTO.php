<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class KatmPresenceReportsDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmPresenceReportDTO::class)]
        public ?DataCollection $presence_report,
    ) {}
}
