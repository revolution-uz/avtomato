<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Product;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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
                'type' => Type::nonNull(ProductInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Product
    {
        $product = new Product;
        $product->fill($args['product']);
        $product->slug = $product->name_en;
        $product->saveOrFail();

        $product->syncImagesWithOrder($args['product']['images']);

        return $product;
    }
}
