<?php

namespace App\GraphQL\Ecommerce\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\ProductOptionGroupGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionGroup;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ProductOptionGroupsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'productOptionGroups',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(ProductOptionGroupGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_GROUP_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductOptionGroup::query()
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name_en', 'ILIKE', $search)
                        ->orWhere('name_uz', 'ILIKE', $search)
                        ->orWhere('name_oz', 'ILIKE', $search)
                        ->orWhere('name_ru', 'ILIKE', $search)
                        ->orWhere('slug', 'ILIKE', $search)
                        ->orWhere('description', 'ILIKE', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|ProductOptionGroup $q) => $q->orderBySortable($args['sort']),
                fn (Builder|ProductOptionGroup $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
