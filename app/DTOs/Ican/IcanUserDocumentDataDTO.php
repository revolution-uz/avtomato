<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanUserDocumentDataDTO extends Data
{
    public function __construct(
        public ?string $inn,
        public ?string $pinfl,
        public ?string $gender,
        public ?string $last_name,
        public ?string $birth_date,
        public ?string $first_name,
        public ?string $middle_name,
        public ?string $birth_address,
        public ?string $passport_number,
        public ?string $passport_series,
        public ?string $passport_issue_date,
        public ?string $passport_expiry_date,
        public ?string $full_name
    ) {}
}
