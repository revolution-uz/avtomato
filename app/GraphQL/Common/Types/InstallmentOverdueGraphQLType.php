<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentOverdueGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentOverdueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Overdue GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'overdue_days' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The overdue days of installments',
                'resolve' => function (array $root) {
                    return $root['overdue_days'];
                },
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'The sum of amount of overdue of installments',
                'resolve' => function (array $root) {
                    return $root['amount'];
                },
            ],
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of overdue of installments',
                'resolve' => function (array $root) {
                    return $root['count'];
                },
            ],
        ];
    }
}
