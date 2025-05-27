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
                'type' => Type::nonNull(ProductBrandInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductBrandGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_BRAND_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductBrand
    {
        $brand = new ProductBrand;
        $brand->fill($args['productBrand']);
        $brand->slug = $args['productBrand']['name_en'];
        $brand->saveOrFail();

        return $brand;
    }
}
