<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\KatmClientDTO;

class KatmCreditHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmCreditHistoryAdditionalInfoDTO $additional_info,
        public KatmCreditHistoryScoreDTO $score,
        public KatmClientDTO $client,
    ) {}
}
