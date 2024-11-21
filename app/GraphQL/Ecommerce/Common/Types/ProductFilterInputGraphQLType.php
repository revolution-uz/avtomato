<?php

namespace App\GraphQL\Ecommerce\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
                    'exists:ecommerce_product_categories,id',
                ],
            ],
            'brand_id' => [
                'name' => 'product_brand_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'exists:ecommerce_product_brands,id',
                ],
            ],
            'min_price' => [
                'name' => 'min_price',
                'type' => Type::float(),
                'rules' => [
                    'nullable',
                    'numeric',
                ],
            ],
            'max_price' => [
                'name' => 'max_price',
                'type' => Type::float(),
                'rules' => [
                    'nullable',
                    'numeric',
                ],
            ],
            'options' => [
                'name' => 'options',
                'type' => Type::listOf(GraphQL::type(ProductOptionFilterInputGraphQLType::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'nullable',
                ],
            ],
        ];
    }
}
