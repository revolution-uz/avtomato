<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentAmountsByIncreasingValueGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentAmountsByIncreasingValueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment or Transaction Amount, Count and Percent GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of installments or transactions',
                'resolve' => function (array $root) {
                    return $root['count'];
                },
            ],
            'count_increasing_percent' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The percent of increasing of count of installments or transactions',
                'resolve' => function (array $root) {
                    return $root['count_increasing_percent'];
                },
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of amount of installments or transactions',
                'resolve' => function (array $root) {
                    return $root['amount'];
                },
            ],
            'amount_increasing_percent' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The percent of increasing of amount of installments or transactions',
                'resolve' => function (array $root) {
                    return $root['amount_increasing_percent'];
                },
            ],
        ];
    }
}
