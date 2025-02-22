<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Mahalla;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class MahallaGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'MahallaGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Mahalla $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Mahalla $root) => $root->name,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Mahalla $root) => $root->name_uz,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Mahalla $root) => $root->name_oz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Mahalla $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Mahalla $root) => $root->name_en,
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Mahalla $root) => $root->district_id,
            ],
            'district' => [
                'type' => Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Mahalla $root) => $root->district,
            ],
        ];
    }
}
