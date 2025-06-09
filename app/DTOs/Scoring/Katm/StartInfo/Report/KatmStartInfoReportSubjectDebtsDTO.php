<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class KatmStartInfoReportSubjectDebtsDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmStartInfoReportSubjectDebtDTO::class)]
        public ?Collection $debts,
    ) {}
}
