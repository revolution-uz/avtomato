<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteAdminGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteAdmin',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
                'rules' => ['required', 'array', 'distinct'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanyAdmin::query()->whereIn('id', $args['ids'])->delete();
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids.*' => [
                'required',
                'integer',
                'exists:admins,id,deleted_at,NULL',
                function ($attribute, $value, $fail) {
                    if ($value === auth()->id()) {
                        $fail('You cannot delete yourself.');
                    }

                    if (
                        CompanyAdmin::query()
                            ->where('role_id', Role::getAdmin()->id)
                            ->where('id', $value)
                            ->exists()
                    ) {
                        $fail('You cannot delete an admin with the admin role.');
                    }
                },
            ],
        ];
    }
}
