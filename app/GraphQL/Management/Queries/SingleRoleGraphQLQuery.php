<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Rbac\Role;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleRoleGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleRole',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:roles,id,deleted_at,NULL'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Role::query()->findOrFail($args['id']);
    }
}
