<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductOptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductOption',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(ProductOption::class, 'id')->whereNull('deleted_at'),
                ],
            ],
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
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOption
    {
        $option = ProductOption::query()->findOrFail($args['id']);
        $option->fill($args['productOption']);
        $option->slug = $args['productOption']['name_en'];
        $option->saveOrFail();

        $option->categories()->sync($args['productOption']['categories']);

        return $option;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'productOption.name_en' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($args) {
                    if (
                        ProductOption::query()->whereNot('id', $args['id'])->where('slug', Str::slug($value))->exists()
                    ) {
                        $fail(trans('validation.unique', ['attribute' => 'name']));
                    }
                },
            ],
            'productOption.categories.*' => [
                'int',
                Rule::exists(ProductCategory::class, 'id')->whereNull('deleted_at'),
            ],
        ]);
    }
}
