<?php

namespace App\GraphQL\Ecommerce\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use Closure;
use GraphQL\Error\Error;
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

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductCategory::query()
            ->when(isset($args['search']), fn (Builder $query) => $query
                ->where('name_en', 'ILIKE', "%{$args['search']}%")
                ->orWhere('name_uz', 'ILIKE', "%{$args['search']}%")
                ->orWhere('name_oz', 'ILIKE', "%{$args['search']}%")
                ->orWhere('name_ru', 'ILIKE', "%{$args['search']}%")
            )
            ->when(
                isset($args['sort']),
                fn (Builder|ProductCategory $q) => $q->orderBySortable($args['sort']),
                fn (Builder|ProductCategory $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
