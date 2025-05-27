<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentMonthlyInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentMonthlyInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['name'],
            ],
            'type' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['type'],
            ],
            'stack' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['stack'],
            ],
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (array $root) => $root['count'],
            ],
            'data' => [
                'type' => Type::nonNull(Type::listOf(Type::float())),
                'resolve' => fn (array $root) => $root['data'],
            ],
        ];
    }
}
