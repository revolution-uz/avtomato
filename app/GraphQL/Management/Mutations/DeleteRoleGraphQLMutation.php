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

class DeleteRoleGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteRole',
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
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Role::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Role::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if ($value == Role::getAdmin()->id) {
                        $fail(__('api.You cannot delete', ['item' => Role::query()->where('id', $value)->first()->name]));
                    }
                    if (CompanyAdmin::query()->where('role_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Role'),
                            'element' => __('api.User'),
                        ]));
                    }
                },
            ],
        ];
    }
}
