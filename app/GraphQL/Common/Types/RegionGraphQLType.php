<?php

namespace App\GraphQL\Common\Types;

use App\Models\Company\Region;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RegionGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'RegionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Region $root) => $root->id,
            ],
            'name' => [
                'type' => Type::string(),
                'resolve' => fn (Region $root) => $root->name,
            ],
            'name_uz' => [
                'type' => Type::string(),
                'resolve' => fn (Region $root) => $root->name_uz,
            ],
            'name_oz' => [
                'type' => Type::string(),
                'resolve' => fn (Region $root) => $root->name_oz,
            ],
            'name_ru' => [
                'type' => Type::string(),
                'resolve' => fn (Region $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::string(),
                'resolve' => fn (Region $root) => $root->name_en,
            ],
            'districts' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => fn (Region $root) => $root->districts,
            ],
        ];
    }
}
