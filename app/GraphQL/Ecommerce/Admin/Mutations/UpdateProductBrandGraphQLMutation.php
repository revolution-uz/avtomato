<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductBrand;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductBrandGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductBrand',
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
                    Rule::exists(ProductBrand::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'productBrand' => [
                'name' => 'productBrand',
                'type' => Type::nonNull(ProductBrandInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductBrandGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_BRAND_DELETE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductBrand
    {
        $brand = ProductBrand::query()->findOrFail($args['id']);
        $brand->fill($args['productBrand']);
        $brand->slug = $args['productBrand']['name_en'];
        $brand->saveOrFail();

        return $brand;
    }

    public function rules(?array $args = []): array
    {
        return array_merge(parent::rules(), [
            'productBrand.name_en' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($args) {
                    if (ProductBrand::query()->whereNot('id', $args['id'])->where('slug', Str::slug($value))->exists()) {
                        $fail(trans('validation.unique', ['attribute' => 'name']));
                    }
                },
            ],
        ]);
    }
}
