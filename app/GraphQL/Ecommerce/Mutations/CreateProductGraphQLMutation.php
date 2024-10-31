<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductInputGraphQLType;
use App\Models\Company\Ecommerce\Product;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProduct',
    ];

    public function args(): array
    {
        return [
            'product' => [
                'name' => 'product',
                'type' => Type::nonNull(GraphQL::type(ProductInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Product
    {

        $model = new Product;
        $model->fill($args['product']);
        $model->saveOrFail();

        return $model;
    }
}
