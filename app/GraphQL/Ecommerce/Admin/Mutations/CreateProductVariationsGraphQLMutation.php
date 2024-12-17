<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductVariationsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductVariations',
    ];

    public function args(): array
    {
        return [
            'productVariations' => [
                'name' => 'productVariations',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(ProductVariationInputGraphQLType::type()))),
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
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        $allValueIds = collect($args['productVariations'])->pluck('values')->flatten()->unique();
        $optionValues = ProductOptionValue::query()->whereIn('id', $allValueIds)->get()->keyBy('id');

        foreach ($args['productVariations'] as $item) {
            $variation = ProductVariation::query()->create(
                Arr::only($item, ['product_id', 'is_active', 'cost', 'actual_price', 'old_price'])
            );

            $attachData = [];
            foreach ($item['values'] as $value_id) {
                $value = $optionValues->get($value_id);
                if ($value) {
                    $attachData[$value_id] = [
                        'product_id' => $variation->product_id,
                        'option_id' => $value->option_id,
                    ];
                }
            }
            $variation->optionValues()->attach($attachData);

            if (! empty($item['images'])) {
                $variation->syncImagesWithOrder($item['images']);
            }
        }

        return true;
    }
}
