<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Carbon\Carbon;
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
    ) {
    }

    public static function fromUnired($response): static
    {
        $result = [];
        foreach ($response as $month => $data) {
            $detailsKey = $month . '_details';
            $details = null;
            $is_month = ! str_contains($month, 'details');
            if (isset($response[$detailsKey])) {
                $details = [];
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
                $result[] = [
                    'month' => Carbon::make($month),
                    'full_amount' => $data,
                    'details' => $details,
                ];
            }
        }

        return self::from($result);
    }
}
