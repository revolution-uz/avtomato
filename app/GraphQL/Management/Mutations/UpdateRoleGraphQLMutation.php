<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Exceptions\ForbiddenException;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Rbac\Role;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateRoleGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateRole',
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
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Role $role */
        $role = Role::findOrFail($args['id']);

        if ($role->name === UserRoleEnum::ADMIN->value) {
            throw new ForbiddenException('You can\'t update admin role');
        }

        $role->name = $args['name'];
        $role->saveOrFail();

        return $role;
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->withoutTrashed()
                    ->ignore($args['id']),
            ],
        ];
    }
}
