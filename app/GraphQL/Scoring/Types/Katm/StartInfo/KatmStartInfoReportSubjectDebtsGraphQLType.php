<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportSubjectDebtsDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportSubjectDebtsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportSubjectDebtsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'debts' => [
                'type' => Type::listOf(GraphQL::type(KatmStartInfoReportSubjectDebtGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (KatmStartInfoReportSubjectDebtsDTO $root) => $root->debts,
            ],
        ];
    }
}
