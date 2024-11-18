<?php

namespace App\GraphQL\Ecommerce\Common\Types;

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
                'resolve' => function (District $root) {
                    return $root->id;
                },
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (District $root) {
                    return $root->region_id;
                },
            ],
            'region' => [
                'type' => Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => function (District $root) {
                    return $root->region;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (District $root) {
                    return $root->name;
                },
            ],
            'mahallas' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => function (District $root) {
                    return $root->mahallas;
                },
            ],
        ];
    }
}
