<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BranchForStatisticsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'BranchForStatisticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (array $root) {
                    return $root['branch_id'];
                },
            ],
            'branch_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return $root['branch_name'];
                },
            ],
            'types' => [
                'type' => Type::listOf(GraphQL::type(TypeTotalGraphQLType::GRAPHQL_TYPE_NAME)), // Define TypeTotal to hold type and amount
                'resolve' => function (array $root) {
                    return $root['types'];
                },
            ],
            'uzcard_total_amount' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return number_format($root['uzcard_total_amount'], 2, '.', '.');
                },
            ],
            'humo_total_amount' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return number_format($root['humo_total_amount'], 2, '.', '.');
                },
            ],
            'total_amount_by_branch' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return number_format($root['total_amount_by_branch'], 2, '.', '.');
                },
            ],
        ];
    }
}
