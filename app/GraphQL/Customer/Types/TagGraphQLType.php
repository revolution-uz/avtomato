<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Tag;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TagGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TagGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tag $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tag $root) => $root->name,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tag $root) => $root->name_en,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tag $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tag $root) => $root->name_ru,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tag $root) => $root->name_oz,
            ],
        ];
    }
}
