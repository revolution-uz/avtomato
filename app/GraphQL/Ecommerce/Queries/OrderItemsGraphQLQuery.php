<?php

namespace App\GraphQL\Ecommerce\Queries;

use App\GraphQL\Ecommerce\Types\OrderItemGraphQLType;
use App\Models\Company\Ecommerce\OrderItem;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class OrderItemsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'orderItems',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(OrderItemGraphQLType::GRAPHQL_TYPE_NAME);
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
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return OrderItem::query()->with('product')
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->whereHas('product', function (Builder $q) use ($search) {
                    $q->where('name_uz', 'ilike', $search)
                        ->orWhere('name_en', 'ilike', $search)
                        ->orWhere('name_oz', 'ilike', $search)
                        ->orWhere('name_ru', 'ilike', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|OrderItem $q) => $q->orderBySortable($args['sort']),
                fn (Builder|OrderItem $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
