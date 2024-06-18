<?php

namespace App\GraphQL\User\Types;

use App\Models\Company\Branch;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionDetailsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PermissionDetailsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Permission Details GraphQL Type',
    ];

    public function fields(): array
    {
        $branchesCount = Branch::query()->count();
        $branchPermissions = [];

        foreach (range(1, $branchesCount) as $branchIndex) {
            $branchPermissions['branch_' . $branchIndex] = [
                'type' => Type::boolean(),
                'description' => 'All branches permission',
                'resolve' => fn ($root) => $root['branch_' . $branchIndex] ?? false,
            ];
        }

        return [
            'all' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'All branches permission',
                'resolve' => function ($root) {
                    return $root['all'] ?? false;
                },
            ],
            ...$branchPermissions,
        ];
    }
}
