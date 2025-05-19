<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\District;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DistrictGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'DistrictGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (District $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (District $root) => $root->name,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (District $root) => $root->name_uz,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (District $root) => $root->name_oz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (District $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (District $root) => $root->name_en,
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (District $root) => $root->region_id,
            ],
            'region' => [
                'type' => Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (District $root) => $root->region,
            ],
            'mahallas' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME)))),
                'resolve' => fn (District $root) => $root->mahallas,
            ],
        ];
    }
}
