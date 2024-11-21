<?php

namespace App\GraphQL\Ecommerce\Common\Types;

use App\Models\Company\Region;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'districts' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => function (Region $root) {
                    return $root->districts;
                },
            ],
        ];
    }
}
