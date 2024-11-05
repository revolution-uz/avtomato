<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:e_commerce_product_categories,id',
                ],
            ],
            'brand_id' => [
                'name' => 'product_brand_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'nullable',
                    'exists:e_commerce_product_brands,id',
                ],
            ],
            'name_uz' => [
                'name' => 'name_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'name_ru' => [
                'name' => 'name_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'name_en' => [
                'name' => 'name_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'cost' => [
                'name' => 'cost',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'required',
                    'numeric',
                ],
            ],
            'actual_price' => [
                'name' => 'actual_price',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'required',
                    'numeric',
                ],
            ],
            'old_price' => [
                'name' => 'old_price',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'nullable',
                    'numeric',
                ],
            ],
            'is_active' => [
                'name' => 'is_active',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => [
                    'required',
                    'boolean',
                ],
            ],

        ];
    }
}
