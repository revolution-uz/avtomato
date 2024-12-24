<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Product;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProduct',
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
                    Rule::exists(Product::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'product' => [
                'name' => 'product',
                'type' => Type::nonNull(ProductInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(ProductGraphQLType::type());
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
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Product
    {
        $product = Product::query()->findOrFail($args['id']);
        $args['product']['slug'] = $args['product']['name_en'];
        $product->update($args['product']);

        $product->syncImagesWithOrder($args['product']['images']);

        return $product;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'product.name_en' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($args) {
                    if (
                        Product::query()->whereNot('id', $args['id'])->where('slug', Str::slug($value))->exists()
                    ) {
                        $fail(trans('validation.unique', ['attribute' => 'name']));
                    }
                },
            ],
        ]);
    }
}
