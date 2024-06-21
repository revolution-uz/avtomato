<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\Rbac\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoleGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'RoleGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Role GraphQL Type',
        'model' => Role::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of attachment',
                'resolve' => function (Role $root) {
                    return $root->id;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Attachment url',
                'resolve' => function (Role $root) {
                    return $root->name;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => function (Role $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment updated timestamp',
                'resolve' => function (Role $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Attachment deleted timestamp',
                'resolve' => function (Role $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
            'permissions' => [
                'type' => GraphQL::type(PermissionGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Permissions of admin',
                'resolve' => function (Role $root) {
                    return $root->mapRolePermissionByBranch();
                },
            ],
        ];
    }
}
