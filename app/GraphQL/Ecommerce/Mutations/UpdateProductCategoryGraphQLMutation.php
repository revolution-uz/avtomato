<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductCategoryGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductCategory',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:e_commerce_product_categories,id'],
            ],
            'productCategoryInput' => [
                'name' => 'productCategoryInput',
                'type' => Type::nonNull(GraphQL::type(ProductCategoryInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductCategory
    {

        $model = ProductCategory::query()->findOrFail($args['id']);
        $model->update($args['productCategory']);
        $model->refresh();

        return $model;
    }
}
