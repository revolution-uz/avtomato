<?php

namespace App\GraphQL\User\Types;

use App\Enums\Company\Rbac\PermissionEnum;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Permission GraphQL Type',
    ];

    public function fields(): array
    {
        $fields = [];

        foreach (PermissionEnum::cases() as $permission) {
            $fields[$permission->value] = [
                'type' => Type::listOf(GraphQL::type(PermissionDetailsGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'One permission item',
                'resolve' => fn (array $root) => $root[$permission->value],
            ];
        }

        return $fields;
    }
}
