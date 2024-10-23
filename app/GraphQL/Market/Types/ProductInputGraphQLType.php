<?php

namespace App\GraphQL\Market\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'product_category_id' => [
                'name' => 'product_category_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:product_categories,id',
                ],
            ],
            'product_brand_id' => [
                'name' => 'product_brand_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:product_brands,id',
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
            'image' => [
                'name' => 'image',
                'type' => GraphQL::type('Upload'),
                'rules' => [
                    'required',
                    'mimes:jpg,jpeg,png',
                    'max:1024',
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

        ];
    }
}
