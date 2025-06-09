<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportSubjectDebtDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportSubjectDebtGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportSubjectDebtGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'org_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->org_type,
            ],
            'org_name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->org_name,
            ],
            'currency' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->currency,
            ],
            'all_debts' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->all_debts,
            ],
            'curr_debts' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->curr_debts,
            ],
            'last_update' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSubjectDebtDTO $root) => $root->last_update,
            ],
        ];
    }
}
