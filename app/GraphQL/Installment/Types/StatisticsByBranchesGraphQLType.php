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
        'description' => 'Branch and its total amount grouped by type',
    ];

    public function fields(): array
    {
        return [
            'branches' => [
                'type' => Type::listOf(GraphQL::type('BranchGraphQLType')),
                'description' => 'A list of branches and their respective types and total amounts',
                'resolve' => function (array $root) {
                    return $root['branches'];
                },
            ],
            'total_amount_by_type' => [
                'type' => Type::listOf(GraphQL::type(TypeTotalGraphQLType::GRAPHQL_TYPE_NAME)), // Since "total_amount_by_type" is a list of floats
                'description' => 'Total amount grouped by each type',
                'resolve' => function (array $root) {
                    $result = [];
                    foreach ($root['total_amount_by_type'] as $typeId => $totalAmount) {
                        $result[] = [
                            'type' => $typeId,
                            'total_amount' => $totalAmount,
                        ]; // Ensure the type is mapped to the total amount
                    }

                    return $result;
                },
            ],
            'full_total_amount' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The overall total amount across all branches',
                'resolve' => function (array $root) {
                    return $root['full_total_amount'];
                },
            ],
        ];
    }
}
