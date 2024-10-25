<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class ProductCategoryInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductCategoryInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
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
            'image' => [
                'name' => 'image',
                'type' => GraphQL::type('Upload'),
                'rules' => [
                    'required',
                    'mimes:jpg,jpeg,png',
                    'max:1024',
                ],
            ],
        ];
    }
}
