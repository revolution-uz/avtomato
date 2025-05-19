<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\KatmClientDTO;

class KatmInpsHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmClientDTO $client,
        public KatmIncomesPeriodDTO $incomes_period,
        public KatmPresenceReportsDTO $presence_reports,
        public KatmIncomesDTO $incomes,
    ) {}
}
