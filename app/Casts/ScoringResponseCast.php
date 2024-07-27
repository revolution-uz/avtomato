<?php

namespace App\Casts;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomeDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomesDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmInpsHistoryResponseDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportsDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Spatie\LaravelData\DataCollection;

class ScoringResponseCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BaseScoringResponse
    {
        if ($value === null) {
            return null;
        }

        return match ($attributes['type']) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value => KatmCreditHistoryResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::KATM_INPS_HISTORY->value => KatmInpsHistoryResponseDTO::from($this->convertIntoValidForm($value)),
            ScoringRequestTypeEnum::KATM_PENSION_INFO->value => KatmPensionInfoResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_MIB_INFO->value => MibInfoResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_TAX_INFO->value => TaxInfoResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::HUMO->value,
            ScoringRequestTypeEnum::UZCARD->value => CardScoringResponseDTO::from(json_decode($value, true)),
            default => throw new InvalidArgumentException,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value instanceof BaseScoringResponse) {
            return json_encode($value->toArray());
        }

        throw new InvalidArgumentException;
    }

    private function convertIntoValidForm($value): array
    {
        $value = json_decode($value, true);

        return [
            'client' => $value['client'],
            'incomes_period' => $value['incomes_period'],
            'presence_reports' => new KatmPresenceReportsDTO(
                new DataCollection(KatmPresenceReportDTO::class, $value['presence_reports']['presence_report'])
            ),
            'incomes' => new KatmIncomesDTO(
                new DataCollection(KatmIncomeDTO::class, $value['incomes']['income'])
            ),
        ];
    }
}
