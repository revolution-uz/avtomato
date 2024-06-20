<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Exceptions\ForbiddenException;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Rbac\Role;
use App\Models\Company\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteRoleGraphQLMutation extends Mutation
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
            'ids' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Role $role */
        $role = Role::findOrFail($args['id']);

        if ($role->name === UserRoleEnum::ADMIN->value) {
            throw new ForbiddenException('You can\'t update admin role');
        }

        $role->name = $args['name'];

        return $role->saveOrFail();
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
                'array',
                function ($attribute, $value, $fail) {
                    if (! Role::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if ($value == Role::getAdmin()->id) {
                        $fail(__('api.You cannot delete', ['item' => Role::query()->where('id', $value)->first()->name]));
                    }
                    if (User::query()->where('role_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Role'),
                            'element' => __('api.User'),
                        ]));
                    }
                },
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->withoutTrashed()
                    ->ignore($this->id),
            ],
        ];
    }
}
