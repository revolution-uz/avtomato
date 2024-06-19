<?php

namespace App\GraphQL\User\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\User\Types\AdminGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\User\UserService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ChangeProfileGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeProfile',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::string(),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return (new UserService)->updateDetails(auth()->user(), $args);
    }

    protected function rules(array $args = []): array
    {
        return [
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
        ];
    }
}
