<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\ProductGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Item;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ProductsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'products',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(ProductGraphQLType::GRAPHQL_TYPE_NAME);
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
            'shop_codes' => [
                'type' => Type::listOf(Type::string()),
                'rules' => ['array', 'distinct', 'min:1'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_PRODUCT_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Item::query()
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->where('external_code', 'ILIKE', "%{$args['search']}%")
                ->orWhere('external_name', 'ILIKE', "%{$args['search']}%"))
            ->when(isset($args['shop_codes']), fn (Builder $q) => $q
                ->whereIn('external_shop_code', $args['shop_codes']))
            ->when(
                isset($args['sort']),
                fn (Builder|Item $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Item $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'shop_codes.*' => ['required', 'string'/*'exists:loyalty_program_shops,external_code,deleted_at,NULL'*/],
        ];
    }
}
