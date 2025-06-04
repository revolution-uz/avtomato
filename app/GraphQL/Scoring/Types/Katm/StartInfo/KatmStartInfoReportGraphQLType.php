<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'sysinfo' => [
                'type' => GraphQL::type(KatmStartInfoReportSysInfoGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportDTO $root) => $root->sysinfo,
            ],
            'client' => [
                'type' => GraphQL::type(KatmStartInfoReportClientGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportDTO $root) => $root->client,
            ],
            'subject_claims' => [
                'type' => GraphQL::type(KatmStartInfoReportSubjectClaimsGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportDTO $root) => $root->subject_claims,
            ],
            'subject_debts' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportDTO $root) => $root->subject_debts,
            ],
            'average_monthly_payment' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportDTO $root) => $root->average_monthly_payment,
            ],
        ];
    }
}
