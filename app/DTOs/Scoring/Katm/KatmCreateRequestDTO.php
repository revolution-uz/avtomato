<?php

namespace App\DTOs\Scoring\Katm;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\Models\Company\District;
use App\Models\Company\Region;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class KatmCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $pinfl,
        public string $passport_series,
        public string $passport_number,
        #[WithCast(DateTimeInterfaceCast::class, format: 'd-m-Y')]
        public Carbon $passport_given_date,
        public string $first_name,
        public string $last_name,
        public ?string $middle_name,
        public string $phone,
        public ?string $extra_phone,
        #[WithCast(DateTimeInterfaceCast::class, format: 'd-m-Y')]
        public Carbon $birth_date,
        public int $region_id,
        public int $district_id,
    ) {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'passport_given_date' => $this->passport_given_date->format('d-m-Y'),
            'birth_date' => $this->birth_date->format('d-m-Y'),
        ]);
    }

    public function convertToKatmRequest(): array
    {
        return [
            'pin' => $this->pinfl,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name ?? '',
            'phone' => '+' . $this->phone,
            'extra_phone' => '+' . ($this->extra_phone ?? $this->phone),
            'region_name' => $this->getRegionName(),
            'city_name' => $this->getDistrictName(),
            'birth_date' => $this->birth_date->format('d.m.Y'),
            'passport_seria' => $this->passport_series,
            'passport_number' => $this->passport_number,
            'passport_given_date' => $this->passport_given_date->format('d.m.Y'),
        ];
    }

    public function getRegionName(): string
    {
        return Region::find($this->region_id)->name_uz;
    }

    public function getDistrictName(): string
    {
        return District::find($this->district_id)->name_uz;
    }
}
