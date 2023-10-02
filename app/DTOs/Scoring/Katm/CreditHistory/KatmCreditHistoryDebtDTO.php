<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmCreditHistoryDebtDTO',
    title: 'KatmCreditHistoryDebtDTO',
    properties: [
        new OA\Property(property: 'all_delays_qty', type: 'float', example: '1'),
        new OA\Property(property: 'all_delays_sum', type: 'float', example: '2'),
        new OA\Property(property: 'contracts_wo_delays_qty', type: 'float', example: '3'),
        new OA\Property(property: 'contracts_wo_delays_sum', type: 'float', example: '4'),
        new OA\Property(property: 'expired_percent_qty', type: 'float', example: '5'),
        new OA\Property(property: 'expired_percent_sum', type: 'float', example: '6'),
        new OA\Property(property: 'revised_contracts_qty', type: 'float', example: '7'),
        new OA\Property(property: 'revised_contracts_sum', type: 'float', example: '8'),
        new OA\Property(property: 'delays_less_30_qty', type: 'float', example: '9'),
        new OA\Property(property: 'delays_less_30_sum', type: 'float', example: '10'),
        new OA\Property(property: 'switched_month_qty', type: 'float', example: '11'),
        new OA\Property(property: 'switched_month_sum', type: 'float', example: '12'),
        new OA\Property(property: 'delays_less_60_qty', type: 'float', example: '13'),
        new OA\Property(property: 'delays_less_60_sum', type: 'float', example: '14'),
        new OA\Property(property: 'delays_less_90_qty', type: 'float', example: '15'),
        new OA\Property(property: 'delays_less_90_sum', type: 'float', example: '16'),
        new OA\Property(property: 'delays_more_90_qty', type: 'float', example: '17'),
        new OA\Property(property: 'delays_more_90_sum', type: 'float', example: '18'),
        new OA\Property(property: 'delays_in_court_qty', type: 'float', example: '19'),
        new OA\Property(property: 'delays_in_court_sum', type: 'float', example: '20'),
        new OA\Property(property: 'delays_decommissioned_qty', type: 'float', example: '21'),
        new OA\Property(property: 'delays_decommissioned_sum', type: 'float', example: '22'),
        new OA\Property(property: 'delays_decommissioned_pcnt_qty', type: 'float', example: '23'),
        new OA\Property(property: 'delays_decommissioned_pcnt_sum', type: 'float', example: '24'),
    ],
)]
class KatmCreditHistoryDebtDTO extends BaseScoringResponse
{
    public function __construct(
        public float $all_delays_qty,
        public float $all_delays_sum,
        public float $contracts_wo_delays_qty,
        public float $contracts_wo_delays_sum,
        public float $expired_percent_qty,
        public float $expired_percent_sum,
        public float $revised_contracts_qty,
        public float $revised_contracts_sum,
        public float $delays_less_30_qty,
        public float $delays_less_30_sum,
        public float $switched_month_qty,
        public float $switched_month_sum,
        public float $delays_less_60_qty,
        public float $delays_less_60_sum,
        public float $delays_less_90_qty,
        public float $delays_less_90_sum,
        public float $delays_more_90_qty,
        public float $delays_more_90_sum,
        public float $delays_in_court_qty,
        public float $delays_in_court_sum,
        public float $delays_decommissioned_qty,
        public float $delays_decommissioned_sum,
        public float $delays_decommissioned_pcnt_qty,
        public float $delays_decommissioned_pcnt_sum,
    ) {
    }
}
