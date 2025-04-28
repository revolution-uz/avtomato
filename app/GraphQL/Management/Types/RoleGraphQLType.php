<?php

namespace App\GraphQL\Management\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Rbac\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoleGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'RoleGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => Role::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Role $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Role $root) => $root->name,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Role $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Role $root) => $root->updated_at->timestamp,
            ],
            'permission_groups' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(PermissionGroupGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Role $root) => $root->mapAllPermissionByBranch(),
            ],
            'permissions' => [
                'type' => Type::nonNull(GraphQL::type(PermissionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Role $root) => $root->mapRolePermissionByBranch(),
            ],
        ];
    }
}
