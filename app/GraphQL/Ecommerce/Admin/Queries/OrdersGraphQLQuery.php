<?php

namespace App\GraphQL\Ecommerce\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Order;
use Closure;
use GraphQL\Error\Error;
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

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ORDERS_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Order::query()->with('orderItems.product')
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->whereHas('orderItems.product', function (Builder $q) use ($search) {
                    $q->where('name_uz', 'ilike', $search)
                        ->orWhere('name_en', 'ilike', $search)
                        ->orWhere('name_oz', 'ilike', $search)
                        ->orWhere('name_ru', 'ilike', $search);
                })
                    ->orWhereHas('user', function (Builder $q) use ($search) {
                        $q->where('phone', 'ilike', $search);
                    });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|Order $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Order $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
