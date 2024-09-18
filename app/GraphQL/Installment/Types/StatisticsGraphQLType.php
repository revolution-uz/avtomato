<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StatisticsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'StatisticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'date' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn ($root) => $root->date,
            ],
            'debt_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn ($root) => round($root->debt_amount_sum, 2),
            ],
            'paid_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn ($root) => round($root->paid_amount_sum, 2),
            ],
        ];
    }
}
