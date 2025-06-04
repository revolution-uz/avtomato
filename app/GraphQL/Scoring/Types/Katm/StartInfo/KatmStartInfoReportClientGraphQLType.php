<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportClientDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportClientGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportClientGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'katm_sir' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->katm_sir,
            ],
            'duplicates' => [
                'type' => Type::boolean(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->duplicates,
            ],
            'old_name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->old_name,
            ],
            'name_change' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->name_change,
            ],
            'subject' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->subject,
            ],
            'client_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->client_type,
            ],
            'inn' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->inn,
            ],
            'document_type' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->document_type,
            ],
            'document_serial' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->document_serial,
            ],
            'document_number' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->document_number,
            ],
            'document_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->document_date,
            ],
            'birth_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->birth_date,
            ],
            'nibbd' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->nibbd,
            ],
            'region' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->region,
            ],
            'local_region' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->local_region,
            ],
            'address' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->address,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->phone,
            ],
            'resident' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->resident,
            ],
            'gender' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->gender,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportClientDTO $root) => $root->pinfl,
            ],
        ];
    }
}
