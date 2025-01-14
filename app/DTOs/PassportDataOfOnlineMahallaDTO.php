<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class PassportDataOfOnlineMahallaDTO extends Data
{
    public function __construct(
        public int $pinfl,
        public ?int $tin,
        public ?string $series,
        public ?string $number,
        public ?string $document,
        public string $sur_name,
        public string $name,
        public string $patronymic_name,
        public string $full_name,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $birth_date,
        public string $birth_place,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $given_date,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $expiration_date,
        public string $given_place,
        public ?string $given_org,
        public ?string $living_place,
        public string $address,
        public int $region_id,
        public int $district_id,
        public int $gender,
        public int $citizenship_id,
        public string $citizenship,
    ) {
        if ($this->document !== null) {
            $this->series ??= substr($this->document, 0, 2);
            $this->number ??= substr($this->document, -7);
        }
    }
}
