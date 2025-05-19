<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmCreditHistoryAdditionalInfoDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmCreditHistoryDebtDTO $current_debt,
        public KatmCreditHistoryDebtDTO $closed_debt,
        public float $average_monthly_payment,
        public array $notifications,
    ) {
        $this->notifications = array_values($this->notifications);
    }
}
