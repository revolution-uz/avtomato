<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StatisticsInfoGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'StatisticsInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn ($root) => $root->id,
            ],
            'branch_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn ($root) => $root->branch_name,
            ],
            'customer_fullname' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn ($root) => $root->customer_fullname,
            ],
            'debt_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn ($root) => round($root->debt_amount, 2),
            ],
            'paid_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn ($root) => round($root->paid_amount, 2),
            ],
        ];
    }
}
