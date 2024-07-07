<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DashboardGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'DashboardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Dashboard GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'installment_charts' => [
                'type' => Type::listOf(GraphQL::type(InstallmentMonthlyChangesGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Chart of Installment monthly changes',
                'resolve' => function (array $root) {
                    return $root['installment_charts'];
                },
            ],
            'installment_amounts' => [
                'type' => Type::listOf(GraphQL::type(InstallmentAmountsGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Amount of installments',
                'resolve' => function (array $root) {
                    return $root['installment_amounts'];
                },
            ],
            'installment_statuses' => [
                'type' => Type::listOf(GraphQL::type(InstallmentMonthlyStatusesGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Chart of Installment monthly statuses',
                'resolve' => function (array $root) {
                    return $root['installment_statuses'];
                },
            ],
            'installment_overdues' => [
                'type' => Type::listOf(GraphQL::type(InstallmentOverduesGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Chart of Installment overdues',
                'resolve' => function (array $root) {
                    return $root['installment_overdues'];
                },
            ],
        ];
    }
}
