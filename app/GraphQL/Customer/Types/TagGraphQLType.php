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
        ];
    }
}
