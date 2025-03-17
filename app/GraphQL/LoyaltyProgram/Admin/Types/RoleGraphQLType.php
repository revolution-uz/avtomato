<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Rbac\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoleGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'RoleGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
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
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Role $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
