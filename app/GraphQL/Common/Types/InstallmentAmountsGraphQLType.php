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
        'description' => 'Installment Amounts GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'transaction' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The amount and count of transaction',
                'resolve' => function (array $root) {
                    return $root['transaction'];
                },
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The amount and count of installment',
                'resolve' => function (array $root) {
                    return $root['installment'];
                },
            ],
            'installment_future_profit' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The amount and count of installment future profit',
                'resolve' => function (array $root) {
                    return $root['installment_future_profit'];
                },
            ],
            'installment_overdue' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The amount and count of overdue installment ',
                'resolve' => function (array $root) {
                    return $root['installment_overdue'];
                },
            ],
            'npl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The NPL of overdue installment ',
                'resolve' => function (array $root) {
                    return $root['npl'];
                },
            ],
            'enpl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'The eNPL of overdue installment ',
                'resolve' => function (array $root) {
                    return $root['enpl'];
                },
            ],
        ];
    }
}
