<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionGroupGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionGroupGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (array $root) {
                    return $root['id'];
                },
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return $root['slug'];
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return $root['name'];
                },
            ],
            'permissions' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(PermissionEditableItemGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => function (array $root) {
                    return $root['permissions'];
                },
            ],
        ];
    }
}
