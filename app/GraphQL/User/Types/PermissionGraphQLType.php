<?php

namespace App\GraphQL\User\Types;

use App\Enums\Company\Rbac\PermissionEnum;
use GraphQL\Type\Definition\Type;
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
                'type' => Type::string(),
                'description' => 'One permission item',
                'resolve' => fn (array $root) => isset($root[$permission->value]) ? json_encode($root[$permission->value]) : null,
            ];
        }

        return $fields;
    }
}
