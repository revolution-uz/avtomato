<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Notification;
use App\Services\FirebaseService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class SendNotificationsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'sendNotifications',
    ];

    public function __construct(private readonly FirebaseService $_firebaseService) {}

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_NOTIFICATION_SEND);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        $notifications = Notification::query()->whereIn('id', $args['ids'])->get();

        return $this->_firebaseService->sendNotification($notifications);
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'int', Rule::exists(Notification::class, 'id')],
        ];
    }
}
