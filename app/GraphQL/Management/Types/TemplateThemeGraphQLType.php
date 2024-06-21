<?php

namespace App\GraphQL\Management\Types;

use App\DTOs\UserTemplateThemeDto;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TemplateThemeGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TemplateThemeGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Theme Data Type',
    ];

    public function fields(): array
    {
        return [
            'layout_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Layout name',
                'resolve' => function (UserTemplateThemeDto $root) {
                    return $root->layout_name;
                },
            ],
        ];
    }
}
