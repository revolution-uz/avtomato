<?php

namespace App\GraphQL\Customer\Types;

use App\GraphQL\Info\Types\DistrictGraphQLType;
use App\GraphQL\Info\Types\MahallaGraphQLType;
use App\GraphQL\Info\Types\RegionGraphQLType;
use App\Models\Company\Address;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AddressGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AddressGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Address GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of address',
                'resolve' => function (Address $root) {
                    return $root->id;
                },
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Address',
                'resolve' => function (Address $root) {
                    return $root->address;
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Type of address',
                'resolve' => function (Address $root) {
                    return $root->type;
                },
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Region ID of address',
                'resolve' => function (Address $root) {
                    return $root->region_id;
                },
            ],
            'region' => [
                'type' => Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Region of address',
                'resolve' => function (Address $root) {
                    return $root->region;
                },
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::nonNull(Type::int())),
                'description' => 'District ID of address',
                'resolve' => function (Address $root) {
                    return $root->district_id;
                },
            ],
            'district' => [
                'type' => Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'District of address',
                'resolve' => function (Address $root) {
                    return $root->district;
                },
            ],
            'mahalla_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Mahalla ID of address',
                'resolve' => function (Address $root) {
                    return $root->mahalla_id;
                },
            ],
            'mahalla' => [
                'type' => Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Mahalla of address',
                'resolve' => function (Address $root) {
                    return $root->mahalla;
                },
            ],
        ];
    }
}
