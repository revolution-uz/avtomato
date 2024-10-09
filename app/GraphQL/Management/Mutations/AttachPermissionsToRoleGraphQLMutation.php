<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\PermissionInputGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachPermissionsToRoleGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachPermissionsToRole',
    ];

    public function args(): array
    {
        return [
            'role_id' => [
                'name' => 'role_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:roles,id'],
            ],
            'permissions' => [
                'name' => 'permissions',
                'type' => Type::nonNull(Type::listOf(GraphQL::type(PermissionInputGraphQLType::GRAPHQL_TYPE_NAME))),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_PERMISSIONS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Role $role */
        $role = Role::findOrFail($args['role_id']);
        $filtered = array_filter($args['permissions'], fn ($item) => $item['enabled']);
        // additional validation for permissions
        $mapped = array_reduce(
            $filtered,
            static function ($carry, $item) {
                if ($item['enabled']) {
                    $carry[$item['permission_id']][] = $item['branch_id'];
                }

                return $carry;
            },
            []
        );
        foreach ($mapped as $item) {
            if (in_array(null, $item, true) && count($item) > 1) {
                throw ValidationException::withMessages(['permission_id' => 'You can\'t attach permission to all branches and specific branch at the same time']);
            }
        }

        $role->permissions()->detach();
        foreach ($filtered as $permission) {
            $role->permissions()->attach($permission['permission_id'], ['branch_id' => $permission['branch_id']]);
        }
        $role->refresh();

        return $role;
    }

    public function rules(array $args = []): array
    {
        return [
            'permissions.*.permission_id' => function ($attribute, $value, $fail) {
                $permission = Permission::findOrFail($value);
                if (! $permission->feature->is_active) {
                    $fail(__('api.feature_not_active'));
                }
            },
        ];
    }
}
