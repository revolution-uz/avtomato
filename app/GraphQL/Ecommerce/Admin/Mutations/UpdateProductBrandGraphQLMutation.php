<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductBrandGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductBrand',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_product_brands,id'],
            ],
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

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_BRAND_DELETE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductBrand
    {
        $model = ProductBrand::query()->findOrFail($args['id']);
        $model->update($args['productBrand']);
        $model->refresh();

        return $model;
    }
}
