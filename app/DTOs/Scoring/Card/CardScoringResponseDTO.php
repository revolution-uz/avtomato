<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'CardScoringResponseDTO',
    title: 'CardScoringResponseDTO',
    properties: [
        new OA\Property(property: 'report', type: 'array', items: new OA\Items(ref: '#/components/schemas/MonthlyAnalyticsDTO')),
    ],
)]
class CardScoringResponseDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(MonthlyAnalyticsDTO::class)]
        public ?DataCollection $report,
    ) {}

    public static function parseFromUnired($response): CardScoringResponseDTO
    {
        $report = [];
        foreach ($response as $month => $data) {
            $detailsKey = $month . '_details';
            $details = [];
            $is_month = ! str_contains($month, '_details');
            if (isset($response[$detailsKey])) {
                $_details = (array) $response[$detailsKey];
                foreach ($_details as $detail) {
                    $details[] = [
                        'description' => $detail['description'] ?? '',
                        'type_code' => $detail['type_code'] ?? '',
                        'amount' => $detail['amount'] ?? 0,
                    ];
                }
            }
            if ($is_month) {
                $report[] = [
                    'month' => $month,
                    'full_amount' => $data,
                    'details' => $details,
                ];
            }
        }

        return self::from(['report' => $report]);
    }
}
