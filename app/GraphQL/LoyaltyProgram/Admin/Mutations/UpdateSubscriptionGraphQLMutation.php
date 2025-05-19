<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Subscription;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateSubscriptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateSubscription',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:loyalty_program_subscriptions,id'],
            ],
            'subscription' => [
                'type' => Type::nonNull(GraphQL::type(SubscriptionInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SUBSCRIPTION_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = Subscription::findOrFail($args['id']);
        $model->fill($args['subscription']);
        $model->saveOrFail();

        return $model;
    }
}
