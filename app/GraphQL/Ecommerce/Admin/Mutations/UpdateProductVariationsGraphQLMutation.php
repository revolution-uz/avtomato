<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\UpdateProductVariationInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateProductVariationsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductVariations',
    ];

    public function args(): array
    {
        return [
            'productVariations' => [
                'name' => 'productVariations',
                'type' => Type::nonNull(Type::listOf(GraphQL::type(UpdateProductVariationInputGraphQLType::GRAPHQL_TYPE_NAME))),
                'rules' => 'required|array',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(GraphQL::type(ProductVariationGraphQLType::GRAPHQL_TYPE_NAME)));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_EDIT);
    }

    /**
     * @return ProductOptionValue[]
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): array
    {
        /** @var ProductOptionValue[] $updatedVariations */
        $updatedVariations = [];

        $allValueIds = collect($args['productVariations'])->pluck('values')->flatten()->unique();
        $values = ProductOptionValue::query()->whereIn('id', $allValueIds)->get()->keyBy('id');

        foreach ($args['productVariations'] as $item) {
            $variation = ProductVariation::query()->find($item['id']);
            $variation->update(Arr::only($item, ['is_active', 'cost', 'actual_price', 'old_price']));

            $attachData = collect($item['values'])->mapWithKeys(fn ($value_id) => [$value_id => ['option_id' => $values->get($value_id)->option_id]]);

            $variation->optionValues()->sync($attachData);

            $variation->syncImagesWithOrder($item['images'] ?? []);

            $updatedVariations[] = $variation;
        }

        return $updatedVariations;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'productVariations.*.values.*' => [
                'required',
                'int',
                Rule::exists(ProductOptionValue::class, 'id')->whereNull('deleted_at'),
            ],
        ]);
    }
}
