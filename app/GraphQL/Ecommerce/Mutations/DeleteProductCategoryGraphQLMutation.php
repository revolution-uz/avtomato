<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class DeleteProductCategoryGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProductCategory',
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

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_DELETE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): bool
    {
        ProductCategory::query()->whereIn('id', $args['ids'])->delete();

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
                function ($attribute, $value, $fail) {
                    if (! ProductCategory::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (ProductCategory::query()->whereHas('products')->find($value)) {
                        $fail(__('api.are used', [
                            'item' => __('ecommerce.category'),
                            'element' => __('ecommerce.product'),
                        ]));
                    }
                },

            ],
        ];
    }
}
