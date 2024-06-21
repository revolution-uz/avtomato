<?php

namespace App\GraphQL\Info\Types;

use App\Models\Company\Region;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RegionGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'RegionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Region GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of region',
                'resolve' => function (Region $root) {
                    return $root->id;
                },
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of region',
                'resolve' => function (Region $root) {
                    return $root->name;
                },
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Uz of region',
                'resolve' => function (Region $root) {
                    return $root->name_uz;
                },
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Ru of region',
                'resolve' => function (Region $root) {
                    return $root->name_ru;
                },
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name En of region',
                'resolve' => function (Region $root) {
                    return $root->name_en;
                },
            ],
        ];
    }
}
