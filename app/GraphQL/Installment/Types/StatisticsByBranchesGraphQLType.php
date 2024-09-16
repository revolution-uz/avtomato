<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StatisticsByBranchesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'StatisticsByBranchesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'branches' => [
                'type' => Type::listOf(GraphQL::type(BranchForStatisticsGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => function (array $root) {
                    return $root['branches'];
                },
            ],
            'total_amount_by_type' => [
                'type' => Type::listOf(GraphQL::type(TypeTotalGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => function (array $root) {
                    return $root['total_amount_by_type'];
                },
            ],
            'full_total_amount' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return $root['full_total_amount'];
                },
            ],
        ];
    }
}
