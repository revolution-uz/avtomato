<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandInputGraphQLType;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductBrandGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductBrand',
    ];

    public function args(): array
    {
        return [
            'productBrand' => [
                'name' => 'productBrand',
                'type' => Type::nonNull(GraphQL::type(ProductBrandInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductBrandGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductBrand
    {

        $model = new ProductBrand;
        $model->fill($args['productBrand']);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
