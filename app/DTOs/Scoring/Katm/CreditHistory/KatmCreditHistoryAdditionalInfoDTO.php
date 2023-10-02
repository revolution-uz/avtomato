<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmCreditHistoryAdditionalInfoDTO',
    title: 'KatmCreditHistoryAdditionalInfoDTO',
    properties: [
        new OA\Property(property: 'current_debt', ref: '#/components/schemas/KatmCreditHistoryDebtDTO'),
        new OA\Property(property: 'closed_debt', ref: '#/components/schemas/KatmCreditHistoryDebtDTO'),
        new OA\Property(property: 'average_monthly_payment', type: 'double', example: '22.3'),
        new OA\Property(property: 'notifications', type: 'mixed', example: '?'),
    ],
)]
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
