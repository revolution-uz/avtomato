<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\Helpers\RbacHelper;
use App\Http\Requests\Rbac\RolePermissionRequest;
use App\Models\Company\Rbac\Role;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachPermissionsRoleGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createRole',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_PERMISSIONS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Role $role */
        $role = Role::findOrFail($args['id']);

        $request = new RolePermissionRequest($args);
        $filtered = array_filter($request->validated(), fn ($item) => $item['enabled'] !== false);

        // additional validation for permissions
        $mapped = array_reduce(
            $filtered,
            static function ($carry, $item) {
                $carry[$item['permission_id']][] = $item['branch_id'];

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

    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->where('id', $args['id']),
            ],
            '*.permission_id' => 'required|exists:permissions,id',
            '*.branch_id' => 'nullable|exists:branches,id',
            '*.enabled' => 'required|boolean',
        ];
    }
}
