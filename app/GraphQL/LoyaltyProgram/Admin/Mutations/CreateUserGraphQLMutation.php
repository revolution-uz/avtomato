<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\UserGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\UserInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateUserGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createUser',
    ];

    public function args(): array
    {
        return [
            'user' => [
                'type' => Type::nonNull(GraphQL::type(UserInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_USERS_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = new User;
        $model->fill($args['user']);
        $model->saveOrFail();

        return $model;
    }
}
