<?php

namespace App\GraphQL\LoyaltyProgram\Common\Queries;

use App\GraphQL\LoyaltyProgram\Common\Types\TariffGraphQLType;
use App\Models\Company\LoyaltyProgram\Tariff;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TariffsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'tariffs',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(TariffGraphQLType::GRAPHQL_TYPE_NAME);
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
        return Tariff::query()
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->where('name_uz', 'ILIKE', "%{$args['search']}%")
                ->orWhere('name_ru', 'ILIKE', "%{$args['search']}%"))
            ->when(
                isset($args['sort']),
                fn (Builder|Tariff $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Tariff $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
