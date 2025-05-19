<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmCreditHistoryDebtDTO extends BaseScoringResponse
{
    public function __construct(
        public int $all_delays_qty,
        public float $all_delays_sum,
        public int $contracts_wo_delays_qty,
        public float $contracts_wo_delays_sum,
        public int $expired_percent_qty,
        public float $expired_percent_sum,
        public int $revised_contracts_qty,
        public float $revised_contracts_sum,
        public int $delays_less_30_qty,
        public float $delays_less_30_sum,
        public int $switched_month_qty,
        public float $switched_month_sum,
        public int $delays_less_60_qty,
        public float $delays_less_60_sum,
        public int $delays_less_90_qty,
        public float $delays_less_90_sum,
        public int $delays_more_90_qty,
        public float $delays_more_90_sum,
        public int $delays_in_court_qty,
        public float $delays_in_court_sum,
        public int $delays_decommissioned_qty,
        public float $delays_decommissioned_sum,
        public int $delays_decommissioned_pcnt_qty,
        public float $delays_decommissioned_pcnt_sum,
    ) {}
}
