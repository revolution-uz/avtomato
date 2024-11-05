<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductBrandInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductBrandInputGraphQLType';

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
