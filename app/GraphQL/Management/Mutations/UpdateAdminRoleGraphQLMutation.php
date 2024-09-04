<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Services\User\UserService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class UpdateAdminRoleGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAdminRole',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', Rule::exists('admins', 'id')->whereNull('deleted_at')],
            ],
            'role_id' => [
                'name' => 'role_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', Rule::exists('roles', 'id')->whereNull('deleted_at')],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_CHANGE_ROLE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        (new UserService)->updateDetails(CompanyAdmin::findOrFail($args['id']), ['role_id' => $args['role_id']]);

        return true;
    }
}
