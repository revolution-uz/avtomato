<?php

namespace App\Casts;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmInpsHistoryResponseDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use App\DTOs\Scoring\Plum\Mib\MibHistoryResponseDTO;
use App\DTOs\Scoring\Plum\Tax\TaxHistoryResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ScoringResponseCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BaseScoringResponse
    {
        if ($value === null) {
            return null;
        }

        return match ($attributes['type']) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value => KatmCreditHistoryResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::KATM_INPS_HISTORY->value => KatmInpsHistoryResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::KATM_PENSION_INFO->value => KatmPensionInfoResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_MIB_INFO->value => MibHistoryResponseDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_TAX_INFO->value => TaxHistoryResponseDTO::from(json_decode($value, true)),
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
}
