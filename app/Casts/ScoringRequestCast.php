<?php

namespace App\Casts;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxCreateRequestDTO;
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
            ScoringRequestTypeEnum::PLUM_MIB_INFO->value => MibCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::PLUM_TAX_INFO->value => TaxCreateRequestDTO::from(json_decode($value, true)),
            ScoringRequestTypeEnum::HUMO->value,
            ScoringRequestTypeEnum::UZCARD->value => CardCreateRequestDTO::from(json_decode($value, true)),
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
