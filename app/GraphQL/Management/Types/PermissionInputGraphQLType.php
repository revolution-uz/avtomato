<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class PermissionInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'permission_id' => [
                'name' => 'permission_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|exists:permissions,id',
            ],
            'branch_id' => [
                'name' => 'branch_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:branches,id',
            ],
            'enabled' => [
                'name' => 'enabled',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => 'required|boolean',
            ],
        ];
    }
}
