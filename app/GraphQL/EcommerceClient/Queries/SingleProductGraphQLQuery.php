<?php

namespace App\GraphQL\EcommerceClient\Queries;

use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\Models\Company\Ecommerce\Product;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'SingleProduct',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_products,id'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Product::query()->findOrFail($args['id']);
    }
}
