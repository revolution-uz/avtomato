<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\ShopGraphQLType;
use App\Models\Company\LoyaltyProgram\Shop;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ShopsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'shops',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(ShopGraphQLType::GRAPHQL_TYPE_NAME);
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Shop::query()
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->where('name', 'ILIKE', "%{$args['search']}%")
                ->orWhere('external_code', 'ILIKE', "%{$args['search']}%")
                ->orWhere('external_name', 'ILIKE', "%{$args['search']}%"))
            ->when(
                isset($args['sort']),
                fn (Builder|Shop $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Shop $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
