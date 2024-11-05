<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\EcommerceImageGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Product;
use App\Services\Ecommerce\ProductService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UploadImageProductGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'UploadImageProductCategory',
    ];

    public function args(): array
    {
        return [
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_product,id'],
            ],
            'images' => [
                'name' => 'images',
                'type' => Type::listOf(GraphQL::type('Upload')),
                'rules' => [
                    'required',
                    'array',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME)));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $product = Product::query()->findOrFail($args['product_id']);
        foreach ($args['images'] as $image) {
            (new ProductService)->uploadImage($product, $image);
        }

        return $product->images;
    }

    public function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'images.*' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg',
                'max:10240',
            ],
        ]);
    }
}
