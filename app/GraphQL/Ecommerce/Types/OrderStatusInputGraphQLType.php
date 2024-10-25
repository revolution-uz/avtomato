<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class OrderStatusInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderStatusInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'order_stage_id' => [
                'name' => 'order_stage_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:order_stages,id'],
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
        ];
    }
}
