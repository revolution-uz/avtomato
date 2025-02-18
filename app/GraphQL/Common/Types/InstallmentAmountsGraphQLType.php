<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentAmountsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentAmountsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'transaction' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['transaction'],
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['installment'],
            ],
            'installment_future_profit' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['installment_future_profit'],
            ],
            'installment_overdue' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['installment_overdue'],
            ],
            'npl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['npl'],
            ],
            'enpl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['enpl'],
            ],
        ];
    }
}
