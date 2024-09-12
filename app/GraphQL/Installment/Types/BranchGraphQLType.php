<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BranchGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'BranchGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Branch and its total amount grouped by type',
    ];

    public function fields(): array
    {
        return [
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The branch identifier',
                'resolve' => function (array $root) {
                    return $root['branch_id'];
                },
            ],
            'branch_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The branch name',
                'resolve' => function (array $root) {
                    return $root['branch_name'];
                },
            ],
            'types' => [
                'type' => Type::listOf(GraphQL::type(TypeTotalGraphQLType::GRAPHQL_TYPE_NAME)), // Define TypeTotal to hold type and amount
                'description' => 'A list of types and their total amounts',
                'resolve' => function (array $root) {
                    $result = [];
                    foreach ($root['types'] as $typeId => $totalAmount) {
                        $result[] = [
                            'type' => $typeId,
                            'total_amount' => $totalAmount,
                        ]; // Ensure the type is mapped to the total amount
                    }

                    return $result;
                },
            ],
            'total_amount_by_branch' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'Total amount for this branch',
                'resolve' => function (array $root) {
                    return $root['total_amount_by_branch'];
                },
            ],
        ];
    }
}
