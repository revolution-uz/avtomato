<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Unired\Client\UniredMonitoringCardResponseDTO;
use App\DTOs\Unired\Client\UniredMonitoringCardRowDTO;
use App\Enums\Company\Scoring\CardScoringTypeCodeEnum;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class CardScoringResponseDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(MonthlyAnalyticsDTO::class)]
        public ?DataCollection $report,
    ) {}

    public static function parseFromAsaxiy($response): CardScoringResponseDTO
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

    public static function parseFromUnired(UniredMonitoringCardResponseDTO $dto): self
    {
        $report = [];
        /** @var UniredMonitoringCardRowDTO $transaction */
        foreach ($dto->rows as $transaction) {
            $month = date('Y-m', strtotime($transaction->TRAN_DATE_TIME));
            $amount = $transaction->TRAN_AMT;
            $tranType = $transaction->TRAN_TYPE;

            if (! isset($report[$month])) {
                $report[$month] = [
                    'month' => $month,
                    'full_amount' => 0,
                    'details' => [],
                ];
            }

            $report[$month]['full_amount'] += $amount;

            if (! isset($report[$month]['details'][$tranType])) {
                $report[$month]['details'][$tranType] = 0;
            }

            $report[$month]['details'][$tranType] += $amount;
        }

        $report = array_map(function ($stat) {
            $stat['details'] = array_map(function ($tranType, $sum) {
                return [
                    'description' => CardScoringTypeCodeEnum::getDescription($tranType),
                    'type_code' => $tranType,
                    'amount' => $sum,
                ];
            }, array_keys($stat['details']), $stat['details']);

            return $stat;
        }, $report);

        return self::from(['report' => $report]);
    }
}
