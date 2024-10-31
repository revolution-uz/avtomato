<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class DeleteProductBrandGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProductBrand',
    ];

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //            RbacHelper::checkPermission(PermissionEnum::BANNER_DELETE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): bool
    {
        ProductBrand::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                'exists:e_commerce_product_brands,id',
                function ($attribute, $value, $fail) {
                    if (ProductBrand::query()->whereHas('products')->find($value)) {
                        $fail(__('api.are used', [
                            'item' => __('ecommerce.product_brand'),
                            'element' => __('ecommerce.product'),
                        ]));
                    }
                },

            ],
        ];
    }
}
