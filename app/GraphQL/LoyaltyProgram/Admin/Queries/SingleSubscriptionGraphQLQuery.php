<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Subscription;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleSubscriptionGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleSubscription',
    ];

    public function type(): Type
    {
        return GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Subscription::class, 'id'),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SUBSCRIPTION_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Subscription::query()->findOrFail($args['id']);
    }
}
