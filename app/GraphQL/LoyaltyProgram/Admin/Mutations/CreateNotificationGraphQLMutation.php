<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
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
            'title_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'title_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'body_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:1000',
            ],
            'body_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:1000',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(NotificationGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_NOTIFICATION_SEND);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $notification = new Notification;
        $notification->fill($args);
        $notification->saveOrFail();

        return $notification->refresh();
    }
}
