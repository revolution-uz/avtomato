<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOption;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class DeleteProductOptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProductOption',
    ];

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
                'rules' => 'required|array',
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
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_DELETE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): bool
    {
        ProductOption::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids.*' => [
                'required',
                'integer',
                Rule::exists(ProductOption::class, 'id')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (ProductOption::query()->whereHas('productJoinOptions')->find($value)) {
                        $fail(__('api.cannot_delete', [
                            'item' => __('ecommerce.product_option_value'),
                            'element' => __('ecommerce.product_join_option'),
                        ]));
                    }
                },
            ],
        ];
    }
}
