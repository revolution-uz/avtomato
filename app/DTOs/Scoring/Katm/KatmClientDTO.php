<?php

namespace App\DTOs\Scoring\Katm;

use App\Casts\MarkNullIfEmptyArray;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\WithCast;

#[OA\Schema(
    schema: 'KatmClientDTO',
    title: 'KatmClientDTO',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Yura'),
        new OA\Property(property: 'birth_date', type: 'string', example: '2005-08-10'),
        new OA\Property(property: 'gender', type: 'string', example: 'M'),
        new OA\Property(property: 'resident', type: 'string', example: 'Резидент'),
        new OA\Property(property: 'address', type: 'string', example: 'Xorazm viloyati Urganch shaxri'),
        new OA\Property(property: 'phone_number', type: 'string', example: '998882223123'),
        new OA\Property(property: 'pinfl', type: 'string', example: '12345678912345'),
        new OA\Property(property: 'inn', type: 'string', example: '12345678912345', nullable: true),
        new OA\Property(property: 'score_level', type: 'string', example: 'СРЕДНИЙ'),
        new OA\Property(property: 'score_date', type: 'string', example: '2023-09-30 17:54'),
        new OA\Property(property: 'document_type', type: 'string', example: 'Биометрический паспорт гражданина Республики Узбекистан'),
        new OA\Property(property: 'document_serial', type: 'string', example: 'AA'),
        new OA\Property(property: 'document_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'document_date', type: 'string', example: '2022-01-12'),
        new OA\Property(property: 'document_type_id', type: 'string', example: '?', nullable: true),
        new OA\Property(property: 'subject_id', type: 'string', example: '?', nullable: true),
        new OA\Property(property: 'credit_start_date', type: 'string', example: '?', nullable: true),
    ],
)]
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
