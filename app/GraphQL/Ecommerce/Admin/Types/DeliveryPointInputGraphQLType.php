<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class DeliveryPointInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'DeliveryPointInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:addresses,id',
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
            'description_uz' => [
                'name' => 'description_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'description_ru' => [
                'name' => 'description_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'description_en' => [
                'name' => 'description_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'description_oz' => [
                'name' => 'description_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],

            'is_prepaid' => [
                'name' => 'is_prepaid',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => [
                    'required',
                    'boolean',
                ],
            ],
        ];
    }
}
