<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionBranchItemGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionBranchItemGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
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
            'editable' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => function (array $root) {
                    return $root['editable'];
                },
            ],
            'enabled' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => function (array $root) {
                    return $root['enabled'];
                },
            ],
        ];
    }
}
