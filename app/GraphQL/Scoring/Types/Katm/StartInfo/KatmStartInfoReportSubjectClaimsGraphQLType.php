<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportSubjectClaimsDTO;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportSubjectClaimsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportSubjectClaimsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'bank_claims' => [
                'type' => GraphQL::type(KatmStartInfoReportSubjectClaimGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportSubjectClaimsDTO $root) => $root->bank_claims,
            ],
            'leasing_claims' => [
                'type' => GraphQL::type(KatmStartInfoReportSubjectClaimGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportSubjectClaimsDTO $root) => $root->leasing_claims,
            ],
            'lombard_claims' => [
                'type' => GraphQL::type(KatmStartInfoReportSubjectClaimGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReportSubjectClaimsDTO $root) => $root->lombard_claims,
            ],
        ];
    }
}
