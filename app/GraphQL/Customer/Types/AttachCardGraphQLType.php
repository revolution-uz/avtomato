<?php

namespace App\GraphQL\Customer\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachCardGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AttachCardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'card' => [
                'type' => Type::nonNull(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root[0],
            ],
            'attachData' => [
                'type' => Type::nonNull(GraphQL::type(AttachCardDataGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root[1],
            ],
        ];
    }
}
