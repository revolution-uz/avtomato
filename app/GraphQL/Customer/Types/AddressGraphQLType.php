<?php

namespace App\GraphQL\Customer\Types;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Address;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AddressGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const GRAPHQL_TYPE_NAME = 'AddressGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->id,
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Address $root) => $root->address,
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->type->value,
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->region_id,
            ],
            'region' => [
                'type' => Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Address $root) => $root->region,
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->district_id,
            ],
            'district' => [
                'type' => Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Address $root) => $root->district,
            ],
            'mahalla_id' => [
                'type' => Type::int(),
                'resolve' => fn (Address $root) => $root->mahalla_id,
            ],
            'mahalla' => [
                'type' => GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Address $root) => $root->mahalla,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Address $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Address $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
