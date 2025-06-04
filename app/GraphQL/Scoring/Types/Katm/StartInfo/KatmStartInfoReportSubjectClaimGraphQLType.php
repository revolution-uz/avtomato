<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportSubjectClaimDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportSubjectClaimGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportSubjectClaimGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'org_type' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportSubjectClaimDTO $root) => $root->org_type,
            ],
            'claims_qty' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportSubjectClaimDTO $root) => $root->claims_qty,
            ],
            'rejected_qty' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportSubjectClaimDTO $root) => $root->rejected_qty,
            ],
            'granted_qty' => [
                'type' => Type::float(),
                'resolve' => fn (KatmStartInfoReportSubjectClaimDTO $root) => $root->granted_qty,
            ],
        ];
    }
}
