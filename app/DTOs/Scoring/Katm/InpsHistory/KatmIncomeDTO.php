<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmIncomeDTO extends BaseScoringResponse
{
    public function __construct(
        public string $num,
        public string $period,
        public string $orgname,
        public string $org_inn,
        public string $income_summa,
        public string $inps_summa,
        public string $send_date,
    ) {}
}
