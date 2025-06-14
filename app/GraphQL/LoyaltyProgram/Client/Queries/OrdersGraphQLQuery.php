<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\OrderGraphQLType;
use App\Models\Company\LoyaltyProgram\Order;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class OrdersGraphQLQuery extends Query
{
    const int ALL = 0;
    const int MY_ORDERS = 1;
    const int USED_DISTRIBUTORS = 2;
    const int USED_BY_RECIPIENTS = 3;

    protected $attributes = [
        'name' => 'orders',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(OrderGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'regex:/^[-]?[a-z_]+$/'],
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'min:1'],
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'min:5', 'max:100'],
            ],
            'from' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'date_format:Y-m-d'],
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            ],
            'view_filter' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', Rule::in([self::ALL, self::MY_ORDERS, self::USED_DISTRIBUTORS, self::USED_BY_RECIPIENTS])],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = auth('loyalty-program-client')->user();
        $subscriptions = $user->subscriptions->pluck('id');
        $viewFilter = $args['view_filter'] ?? self::ALL;

        return Order::query()
            ->when($viewFilter === self::ALL, fn ($q) => $q
                ->where('user_id', $user->id)
                ->orWhereIn('subscription_id', $subscriptions)
            )
            ->when($viewFilter === self::MY_ORDERS, fn ($q) => $q
                ->where('user_id', $user->id)
            )
            ->when($viewFilter === self::USED_DISTRIBUTORS, fn ($q) => $q
                ->where('user_id', $user->id)
                ->whereNotIn('subscription_id', $subscriptions)
            )
            ->when($viewFilter === self::USED_BY_RECIPIENTS, fn ($q) => $q
                ->whereNot('user_id', $user->id)
                ->whereIn('subscription_id', $subscriptions)
            )
            ->when(isset($args['from']), fn (Builder|Order $q) => $q
                ->whereDate('created_at', '>=', $args['from'])
            )
            ->when(isset($args['to']), fn (Builder|Order $q) => $q
                ->whereDate('created_at', '<=', $args['to'])
            )
            ->when(
                isset($args['sort']),
                fn (Builder|Order $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Order $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
