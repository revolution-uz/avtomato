<?php

namespace App\GraphQL\Customer\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UniredCardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UniredCardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['pinfl'],
            ],
            'uuid' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['uuid'],
            ],
            'pan' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['pan'],
            ],
            'owner' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['owner'],
            ],
            'auto' => [
                'type' => Type::boolean(),
                'resolve' => fn (array $root) => $root['auto'],
            ],
            'is_verified' => [
                'type' => Type::boolean(),
                'resolve' => fn (array $root) => $root['is_verified'],
            ],
            'is_blocked' => [
                'type' => Type::boolean(),
                'resolve' => fn (array $root) => $root['is_blocked'],
            ],
            'block_reason' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['block_reason'],
            ],
            'blocked_at' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['blocked_at->timestamp'],
            ],
        ];
    }
}
