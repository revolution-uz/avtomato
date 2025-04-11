<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Notification;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateNotificationGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createNotification',
    ];

    public function args(): array
    {
        return [
            'notification' => [
                'type' => Type::nonNull(GraphQL::type(NotificationInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(NotificationGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_NOTIFICATION_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $notification = new Notification;
        $notification->fill($args['notification']);
        $notification->saveOrFail();

        return $notification;
    }
}
