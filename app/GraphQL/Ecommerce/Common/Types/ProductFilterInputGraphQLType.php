<?php

namespace App\GraphQL\Ecommerce\Common\Types;

use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class ProductFilterInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductFilterInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(ProductCategory::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'brand_id' => [
                'name' => 'brand_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(ProductBrand::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'min_price' => [
                'name' => 'min_price',
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'max_price' => [
                'name' => 'max_price',
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'option_values' => [
                'name' => 'option_values',
                'type' => Type::listOf(Type::nonNull(Type::int())),
            ],
        ];
    }
}
