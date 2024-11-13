<?php

namespace App\GraphQL\EcommerceClient\Common\Queries;

use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\Models\Company\Ecommerce\ProductCategory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ProductCategoriesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'productCategories',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME);
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
        return ProductCategory::query()
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name_en', 'ILIKE', $search)
                        ->orWhere('name_uz', 'ILIKE', $search)
                        ->orWhere('name_oz', 'ILIKE', $search)
                        ->orWhere('name_ru', 'ILIKE', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|ProductCategory $q) => $q->orderBySortable($args['sort']),
                fn (Builder|ProductCategory $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
