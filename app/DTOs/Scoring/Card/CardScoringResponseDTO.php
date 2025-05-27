<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Unired\Client\UniredMonitoringHumoResponseDTO;
use App\DTOs\Unired\Client\UniredMonitoringUzcardResponseDTO;
use App\Enums\Company\Scoring\CardScoringTypeCodeEnum;
use Illuminate\Support\Collection;
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

    public static function parseFromUniredHumo(UniredMonitoringHumoResponseDTO $dto): self
    {
        $report = self::processTransactions(
            $dto->rows,
            fn ($transaction) => $transaction->TRAN_DATE_TIME->format('Y-m'),
            fn ($transaction) => $transaction->TRAN_AMT,
            fn ($transaction) => $transaction->TRAN_TYPE
        );

        return self::from(['report' => $report]);
    }

    public static function parseFromUniredUzcard(UniredMonitoringUzcardResponseDTO $dto, ?self $responseDto = null): self
    {
        $report = self::processTransactions(
            $dto->content,
            fn ($transaction) => $transaction->udate->format('Y-m'),
            fn ($transaction) => $transaction->reqamt,
            fn ($transaction) => $transaction->transType
        );

        if (! $responseDto) {
            return self::from(['report' => $report]);
        }

        foreach ($report as $monthKey => $item) {
            if (isset($responseDto->report[$monthKey])) {
                $responseDto->report[$monthKey]->full_amount += $item['full_amount'];
                $existingDetails = $responseDto->report[$monthKey]->details->toCollection()->keyBy('type_code');

                foreach ($item['details'] as $detail) {
                    if ($existingDetails->has($detail['type_code'])) {
                        $existingDetails[$detail['type_code']]->amount += $detail['amount'];
                    } else {
                        $responseDto->report[$monthKey]->details[] = DetailedAnalyticsInMonthDTO::from($detail);
                    }
                }
            } else {
                $responseDto->report[$monthKey] = MonthlyAnalyticsDTO::from($item);
            }
        }

        return $responseDto;
    }

    private static function processTransactions(Collection $transactions, callable $getMonth, callable $getAmount, callable $getTranType): array
    {
        $report = [];
        foreach ($transactions as $transaction) {
            $month = $getMonth($transaction);
            $amount = $getAmount($transaction) / 100;
            $tranType = $getTranType($transaction);

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

        return array_map(function ($stat) {
            $stat['details'] = array_map(function ($tranType, $sum) {
                return [
                    'description' => CardScoringTypeCodeEnum::getDescription($tranType),
                    'type_code' => $tranType,
                    'amount' => $sum,
                ];
            }, array_keys($stat['details']), $stat['details']);

            return $stat;
        }, $report);
    }
}
