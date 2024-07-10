<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CalculatedGraphGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CalculatedGraphGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['id'] ?? null,
            ],
            'date' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['date'] ?? null,
            ],
            'amount' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['amount'] ?? null,
            ],
            'portion' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['portion'] ?? null,
            ],
            'monthly' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['monthly'] ?? null,
            ],
        ];
    }
}
