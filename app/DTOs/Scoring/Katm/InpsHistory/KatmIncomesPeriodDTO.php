<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmIncomesPeriodDTO extends BaseScoringResponse
{
    public function __construct(
        public string $incomes_period_begin,
        public string $incomes_period_end,
        public string $incomes_all_summa,
    ) {}
}
