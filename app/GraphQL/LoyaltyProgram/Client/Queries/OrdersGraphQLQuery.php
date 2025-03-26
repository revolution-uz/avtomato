<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\OrderGraphQLType;
use App\Models\Company\LoyaltyProgram\Order;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class OrdersGraphQLQuery extends Query
{
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
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
            'from' => [
                'name' => 'from',
                'type' => Type::string(),
                'rules' => 'nullable|required_with:to|date_format:Y-m-d',
            ],
            'to' => [
                'name' => 'to',
                'type' => Type::string(),
                'rules' => 'nullable|required_with:from|date_format:Y-m-d|after_or_equal:from',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Order::query()
            ->where('user_id', auth('loyalty-program-client')->id())
            ->when(
                isset($args['sort']),
                fn (Builder|Order $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Order $q) => $q->orderByDesc('id')
            )
            ->when(
                isset($args['from'], $args['to']),
                fn (Builder|Order $q) => $q
                    ->whereDate('created_at', '>=', $args['from'])
                    ->whereDate('created_at', '<=', $args['to'])
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
