<?php

namespace App\DTOs\Scoring\Katm;

use App\Casts\MarkNullIfEmptyArray;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\WithCast;

class KatmClientDTO extends BaseScoringResponse
{
    public function __construct(
        public string $name,
        public string $birth_date,
        public string $gender,
        public string $address,
        public ?string $phone_number,
        public ?string $resident,
        public string $pinfl,
        #[WithCast(MarkNullIfEmptyArray::class)]
        public ?string $inn,
        public string $document_type,
        public string $document_serial,
        public string $document_number,
        public string $document_date,
        #[WithCast(MarkNullIfEmptyArray::class)]
        public ?string $document_type_id,
        #[WithCast(MarkNullIfEmptyArray::class)]
        public ?string $subject_id,
        #[WithCast(MarkNullIfEmptyArray::class)]
        public ?string $credit_start_date,
    ) {}
}
