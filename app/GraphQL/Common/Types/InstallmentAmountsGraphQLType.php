<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentAmountsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentAmountsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Amounts GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'installment_count' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of installments',
                'resolve' => function (array $root) {
                    return $root['installment_count'];
                },
            ],
            'installment_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of amount of installments',
                'resolve' => function (array $root) {
                    return $root['installment_amount'];
                },
            ],
            'installment_future_profit' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of profit of future installments',
                'resolve' => function (array $root) {
                    return $root['installment_future_profit'];
                },
            ],
            'installment_total_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of amount of all installments',
                'resolve' => function (array $root) {
                    return $root['installment_total_amount'];
                },
            ],
            'installment_overdue_count' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of overdue installment',
                'resolve' => function (array $root) {
                    return $root['installment_overdue_count'];
                },
            ],
            'installment_overdue_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of amount of overdue installment',
                'resolve' => function (array $root) {
                    return $root['installment_overdue_amount'];
                },
            ],
            'npl' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'NPL',
                'resolve' => function (array $root) {
                    return $root['npl'];
                },
            ],
            'enpl' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'eNPL',
                'resolve' => function (array $root) {
                    return $root['enpl'];
                },
            ],
        ];
    }
}
