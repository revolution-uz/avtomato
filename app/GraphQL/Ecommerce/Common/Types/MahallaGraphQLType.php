<?php

namespace App\GraphQL\Ecommerce\Common\Types;

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
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Mahalla $root) {
                    return $root->id;
                },
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Mahalla $root) {
                    return $root->district_id;
                },
            ],
            'district' => [
                'type' => Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => function (Mahalla $root) {
                    return $root->district;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (Mahalla $root) {
                    return $root->name;
                },
            ],
        ];
    }
}
