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

class UpdateAdminPasswordGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAdminPassword',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(CompanyAdmin::class, 'id')->where('is_system', false)->whereNull('deleted_at'),
                ],
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            'password_confirmation' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_CHANGE_PASSWORD);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        (new UserService)->updatePassword(CompanyAdmin::findOrFail($args['id']), $args['password']);

        return true;
    }
}
