<?php

namespace App\GraphQL\Customer\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class TagInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'TagInputGraphQLType';

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
                    'unique:tags,name_uz',
                ],
            ],
            'name_ru' => [
                'name' => 'name_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'unique:tags,name_ru',
                ],
            ],
            'name_en' => [
                'name' => 'name_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'unique:tags,name_en',
                ],
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'unique:tags,name_oz',
                ],
            ],
        ];
    }
}
