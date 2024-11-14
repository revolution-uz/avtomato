<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Services\Ecommerce\ProductService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UploadImageProductBrandGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'UploadImageProductBrand',
    ];

    public function args(): array
    {
        return [
            'brand_id' => [
                'name' => 'brand_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:ecommerce_product_brands,id'],
            ],
            'image' => [
                'name' => 'image',
                'type' => GraphQL::type('Upload'),
                'rules' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg',
                    'max:10240',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_BRAND_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $productBrand = ProductBrand::query()->findOrFail($args['product_id']);
        (new ProductService)->uploadImage($productBrand, $args['image']);

        return $productBrand->image;
    }
}
