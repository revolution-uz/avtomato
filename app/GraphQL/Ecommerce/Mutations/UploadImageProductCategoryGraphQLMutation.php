<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\EcommerceImageGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Services\Ecommerce\ProductService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UploadImageProductCategoryGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'UploadImageProductCategory',
    ];

    public function args(): array
    {
        return [
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_product_categories,id'],
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
        return Type::nonNull(GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME));
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
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $category = ProductCategory::query()->findOrFail($args['category_id']);
        (new ProductService)->uploadImage($category, $args['image']);

        return $category->image;
    }
}
