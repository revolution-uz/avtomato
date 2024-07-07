<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentOverdueGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentOverdueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Overdue GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'overdue_days' => [
                'type' => Type::listOf(Type::int()),
                'description' => 'The overdue days of installments',
                'resolve' => function (array $root) {
                    return $root['overdue_days'];
                },
            ],
            'amount' => [
                'type' => Type::listOf(Type::float()),
                'description' => 'The sum of amount of overdue of installments',
                'resolve' => function (array $root) {
                    return $root['amount'];
                },
            ],
            'count' => [
                'type' => Type::listOf(Type::int()),
                'description' => 'The number of overdue of installments',
                'resolve' => function (array $root) {
                    return $root['count'];
                },
            ],
        ];
    }
}
