<?php

namespace App\DTOs\Customer;

use Spatie\LaravelData\Data;

/**
 * Class CustomerPassportDTO
 *
 * @property string $pinfl
 * @property string $passport_series
 * @property string $passport_number
 */
class CustomerPassportDTO extends Data
{
    public function __construct(
        public string $pinfl,
        public ?string $passport_series,
        public ?string $passport_number,
    ) {}
}
