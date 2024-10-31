<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductInputGraphQLType;
use App\Models\Company\Ecommerce\Product;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProduct',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:e_commerce_products,id'],
            ],
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
        //            returnRbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_UPDATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Product
    {
        $model = Product::query()->findOrFail($args['id']);
        $args['product']['slug'] = Str::slug($args['product']['name_en']);
        $model->update($args['product']);
        $model->refresh();

        return $model;
    }
}
