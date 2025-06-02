<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23ClientDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $katm_sir,
        public ?string $juridical_status,
        public ?string $client_type,
        public ?string $name,
        public ?string $gender,
        public ?string $pinfl,
        public ?string $birth_date,
        public ?int $registration_region,
        public ?int $registration_district,
        public ?string $registration_address,
        public ?string $registration_address_change,
        public ?string $live_address,
        public ?string $live_address_change,
        public ?string $email,
        public ?string $email_change,
        public ?string $inn,
        public ?string $oked,
        public ?string $founders,
        public ?KatmStartInfoReport23ClientPhonesDTO $phones,
    ) {}
}
