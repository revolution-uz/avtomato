<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\ProductVariation;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class UpdateProductVariationInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'UpdateProductVariationInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductVariation::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'is_active' => [
                'name' => 'is_active',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => 'required',
            ],
            'cost' => [
                'name' => 'cost',
                'type' => Type::nonNull(Type::float()),
                'rules' => 'required|numeric|min:0',
            ],
            'actual_price' => [
                'name' => 'actual_price',
                'type' => Type::nonNull(Type::float()),
                'rules' => 'required|numeric|min:0',
            ],
            'old_price' => [
                'name' => 'old_price',
                'type' => Type::float(),
                'rules' => 'nullable|numeric|min:0',
            ],
            'images' => [
                'name' => 'images',
                'rules' => 'nullable|array',
                'type' => Type::listOf(ProductImageInputGraphQLType::type()),
            ],
            'values' => [
                'name' => 'values',
                'type' => Type::nonNull(Type::listOf(Type::int())),
                'rules' => 'required|array',
            ],
        ];
    }
}
