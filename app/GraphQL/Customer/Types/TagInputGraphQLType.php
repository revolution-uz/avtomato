<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Tag;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
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
                    Rule::unique(Tag::class, 'name_uz')->whereNull('deleted_at'),
                ],
            ],
            'name_ru' => [
                'name' => 'name_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    Rule::unique(Tag::class, 'name_ru')->whereNull('deleted_at'),
                ],
            ],
            'name_en' => [
                'name' => 'name_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    Rule::unique(Tag::class, 'name_en')->whereNull('deleted_at'),
                ],
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    Rule::unique(Tag::class, 'name_oz')->whereNull('deleted_at'),
                ],
            ],
        ];
    }
}
