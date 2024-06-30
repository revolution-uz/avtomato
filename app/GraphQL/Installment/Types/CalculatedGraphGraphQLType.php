<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
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
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['amount'] ?? null,
            ],
            'portion' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['portion'] ?? null,
            ],
            'monthly' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['monthly'] ?? null,
            ],
        ];
    }
}
