<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductBrandGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleProductBrand',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductBrandGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductBrand::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductBrand::query()->findOrFail($args['id']);
    }
}
