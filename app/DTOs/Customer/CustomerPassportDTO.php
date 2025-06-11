<?php

namespace App\DTOs\Customer;

use Spatie\LaravelData\Data;

class CustomerPassportDTO extends Data
{
    public function __construct(
        public ?string $pinfl,
        public ?string $passport_series,
        public ?string $passport_number,
        public ?string $passport_id,
    ) {
        if ($this->passport_id && (! $this->passport_series || ! $this->passport_number)) {
            $this->passport_series = substr($this->passport_id, 0, 2);
            $this->passport_number = substr($this->passport_id, 2);
        }
    }
}
