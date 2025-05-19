<?php

namespace App\Casts;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryRequestDTO;
use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ScoringRequestCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BaseScoringRequest
    {
        if ($value === null) {
            return null;
        }

        return match ($attributes['type']) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value,
            ScoringRequestTypeEnum::KATM_INPS_HISTORY->value,
            ScoringRequestTypeEnum::KATM_PENSION_INFO->value => KatmCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_MIB_INFO->value => MibInfoCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_TAX_INFO->value => TaxInfoCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::HUMO->value,
            ScoringRequestTypeEnum::UZCARD->value => CardCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY->value => RoyxatCreditHistoryRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::UZUM_NASIYA_LIMIT->value => UzumNasiyaLimitCreateRequestDTO::from(json_decode($value, true)),
            default => throw new InvalidArgumentException,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value instanceof BaseScoringRequest) {
            return json_encode($value->toArray());
        }

        throw new InvalidArgumentException;
    }
}
