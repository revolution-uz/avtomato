<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductOptionValueGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductOptionValue',
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
                    Rule::exists(ProductOptionValue::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'productOptionValue' => [
                'name' => 'productOptionValue',
                'type' => Type::nonNull(ProductOptionValueInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductOptionValueGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_VALUE_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOptionValue
    {
        $value = ProductOptionValue::query()->findOrFail($args['id']);
        $value->fill($args['productOptionValue']);
        $value->slug = $args['productOptionValue']['value_en'];
        $value->saveOrFail();

        return $value;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'productOptionValue.value_en' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($args) {
                    if (
                        ProductOptionValue::query()->whereNot('id', $args['id'])->where('slug', Str::slug($value))->exists()
                    ) {
                        $fail(trans('validation.unique', ['attribute' => 'value']));
                    }
                },
            ],
        ]);
    }
}
