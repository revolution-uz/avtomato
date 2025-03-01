<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Notification;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateNotificationGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateNotification',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Notification::class, 'id'),
                ],
            ],
            'title_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:255',
            ],
            'title_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:255',
            ],
            'body_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:1000',
            ],
            'body_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|min:3|max:1000',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(NotificationGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_NOTIFICATION_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $notification = Notification::findOrFail($args['id']);
        $notification->fill($args);
        $notification->saveOrFail();

        return $notification->refresh();
    }
}
