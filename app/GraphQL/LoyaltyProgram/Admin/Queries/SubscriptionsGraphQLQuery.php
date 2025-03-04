<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Subscription;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SubscriptionsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'subscriptions',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SUBSCRIPTION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Subscription::factory()->count(29)->create();

        return Subscription::query()
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->whereHas('card', fn ($q) => $q
                    ->where('owner', 'ILIKE', "%{$args['search']}%"))
                ->orWhereHas('tariff', fn ($q) => $q
                    ->where('name_uz', 'ILIKE', "%{$args['search']}%")
                    ->orWhere('name_ru', 'ILIKE', "%{$args['search']}%")))
            ->when(
                isset($args['sort']),
                fn (Builder|Subscription $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Subscription $q) => $q->orderByDesc('id'))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
