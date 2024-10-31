<?php

namespace App\GraphQL\Ecommerce\Queries;

use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\Models\Company\Ecommerce\ProductCategory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductCategoryGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleProductCategory',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductCategory::class, 'id')
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductCategory::query()->findOrFail($args['id']);
    }
}
