<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductOptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductOption',
    ];

    public function args(): array
    {
        return [
            'productOption' => [
                'name' => 'productOption',
                'type' => Type::nonNull(ProductOptionInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductOptionGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOption
    {
        $option = new ProductOption;
        $option->fill($args['productOption']);
        $option->slug = $args['productOption']['name_en'];
        $option->saveOrFail();

        if (! empty($args['productOption']['values'])) {
            ProductOptionValue::query()->whereIn('id', $args['productOption']['values'])->update(['option_id' => $option->id]);
        }

        if (! empty($args['productOption']['categories'])) {
            $option->categories()->attach($args['productOption']['categories']);
        }

        return $option;
    }

    protected function rules(array $args = []): array
    {
        return [
            'productOption.values.*' => [
                'int',
                Rule::exists(ProductOptionValue::class, 'id')->whereNull('deleted_at'),
            ],
            'productOption.categories.*' => [
                'int',
                Rule::exists(ProductCategory::class, 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
