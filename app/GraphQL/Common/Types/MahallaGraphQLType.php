<?php

namespace App\GraphQL\Common\Types;

use App\Models\Company\Mahalla;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class MahallaGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'MahallaGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Mahalla GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'ID of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->id;
                },
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'District ID of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->district_id;
                },
            ],
            'district' => [
                'type' => GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'District of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->district;
                },
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->name;
                },
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Uz of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->name_uz;
                },
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name Ru of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->name_ru;
                },
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name En of mahalla',
                'resolve' => function (Mahalla $root) {
                    return $root->name_en;
                },
            ],
        ];
    }
}
