<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Services\User\UserService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
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
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'old_password' => [
                'name' => 'old_password',
                'type' => Type::nonNull(Type::string()),
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
            ],
            'password_confirmation' => [
                'name' => 'password_confirmation',
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

    protected function rules(array $args = []): array
    {
        return [
            'id' => ['required', Rule::exists('admins', 'id')->whereNull('deleted_at')],
            'old_password' => [
                'required',
                'string',
                'min:8',
                function ($attribute, $value, $fail) use ($args) {
                    $admin = CompanyAdmin::find($args['id']);
                    if (! Hash::check($value, $admin->password)) {
                        $fail('The old password is incorrect.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
