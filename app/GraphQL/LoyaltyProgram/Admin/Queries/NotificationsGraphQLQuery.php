<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Notification;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class NotificationsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'notifications',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(NotificationGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_NOTIFICATION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Notification::query()
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->where('title_uz', 'ILIKE', "%{$args['search']}%")
                ->orWhere('title_ru', 'ILIKE', "%{$args['search']}%")
                ->orWhere('body_uz', 'ILIKE', "%{$args['search']}%")
                ->orWhere('body_ru', 'ILIKE', "%{$args['search']}%"))
            ->when(
                isset($args['sort']),
                fn (Builder|Notification $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Notification $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
