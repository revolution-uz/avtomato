<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionEditableItemGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionEditableItemGraphQLType';

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
                    return $root['slug']->value;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return $root['name'];
                },
            ],
            'branches' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(PermissionBranchItemGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => function (array $root) {
                    return $root['branches'];
                },
            ],
        ];
    }
}
