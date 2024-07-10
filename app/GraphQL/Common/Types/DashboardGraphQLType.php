<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DashboardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'DashboardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Dashboard GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'installment_amounts' => [
                'type' => GraphQL::type(InstallmentAmountsGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Amount of installments',
                'resolve' => function (array $root) {
                    return $root['installment_amounts'];
                },
            ],
            'installment_info_by_status' => [
                'type' => Type::listOf(GraphQL::type(InstallmentMonthlyInfoGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Chart of Installment monthly statuses',
                'resolve' => function (array $root) {
                    return $root['installment_info_by_status'];
                },
            ],
            'installment_overdues' => [
                'type' => Type::listOf(GraphQL::type(InstallmentOverdueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Chart of Installment overdues',
                'resolve' => function (array $root) {
                    return $root['installment_overdues'];
                },
            ],
        ];
    }
}
