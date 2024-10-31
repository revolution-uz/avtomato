<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionInputGraphQLType;
use App\Models\Company\Ecommerce\ProductOption;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductOptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductOption',
    ];

    public function args(): array
    {
        return [
            'productOption' => [
                'name' => 'productOption',
                'type' => Type::nonNull(GraphQL::type(ProductOptionInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductOptionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOption
    {

        $model = new ProductOption;
        $args['productOption']['slug'] = Str::slug($args['productOption']['name_en']);
        $model->fill($args['productOption']);
        $model->saveOrFail();

        return $model;
    }
}
