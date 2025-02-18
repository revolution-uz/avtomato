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
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['count'] ?? null,
            ],
            'count_increasing_percent' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['count_increasing_percent'] ?? null,
            ],
            'current_month_count' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['current_month_count'] ?? null,
            ],
            'amount' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['amount'] ?? null,
            ],
            'amount_increasing_percent' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['amount_increasing_percent'] ?? null,
            ],
            'current_month_amount' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['current_month_amount'] ?? null,
            ],
        ];
    }
}
