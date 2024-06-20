<?php

namespace App\GraphQL\Common\Types;

use App\Models\Company\District;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DistrictGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'DistrictGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'District GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of district',
                'resolve' => function (District $root) {
                    return $root->id;
                },
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Region ID of district',
                'resolve' => function (District $root) {
                    return $root->id;
                },
            ],
            'region' => [
                'type' => Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Region of district',
                'resolve' => function (District $root) {
                    return $root->region;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name of district',
                'resolve' => function (District $root) {
                    return $root->name;
                },
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Uz of district',
                'resolve' => function (District $root) {
                    return $root->name_uz;
                },
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Ru of district',
                'resolve' => function (District $root) {
                    return $root->name_ru;
                },
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name En of district',
                'resolve' => function (District $root) {
                    return $root->name_en;
                },
            ],
        ];
    }
}
