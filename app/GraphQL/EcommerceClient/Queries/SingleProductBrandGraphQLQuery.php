<?php

namespace App\GraphQL\EcommerceClient\Queries;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductBrandGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'SingleProductBrand',
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
                'rules' => ['required', 'exists:e_commerce_product_brands,id'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    /**
     * @throws GeneralException
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (! ProductBrand::query()->where('id', $args['id'])->exists()) {
            throw new GeneralException(trans('api.not found'));
        }

        return ProductBrand::query()->findOrFail($args['id']);
    }
}
