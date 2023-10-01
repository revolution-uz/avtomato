<?php

namespace App\Casts;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
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
            default => throw new InvalidArgumentException(),
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value instanceof BaseScoringRequest) {
            return json_encode($value->toArray());
        }

        throw new InvalidArgumentException();
    }
}
