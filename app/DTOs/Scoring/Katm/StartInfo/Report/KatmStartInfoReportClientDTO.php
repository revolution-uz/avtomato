<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportClientDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $katm_sir,
        public ?bool $duplicates,
        public ?string $old_name,
        public ?string $name_change,
        public ?int $subject,
        public ?string $client_type,
        public ?string $inn,
        public ?int $document_type,
        public ?string $document_serial,
        public ?string $document_number,
        public ?string $document_date,
        public ?string $birth_date,
        public ?string $nibbd,
        public ?int $region,
        public ?int $local_region,
        public ?string $address,
        public ?string $phone,
        public ?string $resident,
        public ?string $gender,
        public ?string $pinfl,
    ) {}
}
