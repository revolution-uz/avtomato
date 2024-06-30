<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class RevokeAdminDeviceGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'revokeAdminDevices',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'admin_id' => [
                'name' => 'admin_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [Rule::exists('users', 'id')->whereNull('deleted_at')],
            ],
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_REVOKE_DEVICE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        PersonalAccessToken::query()
            ->where('tokenable_id', $args['admin_id'])
            ->where('tokenable_type', User::class)
            ->whereIn('id', $args['ids'])
            ->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:personal_access_tokens,id',
        ];
    }
}
