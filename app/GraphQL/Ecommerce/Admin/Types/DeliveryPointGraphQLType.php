<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\Ecommerce\Common\Types\DistrictGraphQLType;
use App\GraphQL\Ecommerce\Common\Types\MahallaGraphQLType;
use App\GraphQL\Ecommerce\Common\Types\RegionGraphQLType;
use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\DeliveryPoint;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DeliveryPointGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'DeliveryPointGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_oz,
            ],
            'description_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_uz,
            ],
            'description_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_ru,
            ],
            'description_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_en,
            ],
            'description_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_oz,
            ],
            'is_prepaid' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (DeliveryPoint $root) => $root->is_prepaid,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->phone,
            ],
            'address_uz' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->address_uz,
            ],
            'address_ru' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->address_ru,
            ],
            'address_en' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->address_en,
            ],
            'address_oz' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->address_oz,
            ],
            'landmark_uz' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->landmark_uz,
            ],
            'landmark_ru' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->landmark_ru,
            ],
            'landmark_en' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->landmark_en,
            ],
            'landmark_oz' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->landmark_oz,
            ],
            'location_url' => [
                'type' => Type::string(),
                'resolve' => fn (DeliveryPoint $root) => $root->location_url,
            ],
            'lat' => [
                'type' => Type::float(),
                'resolve' => fn (DeliveryPoint $root) => $root->lat,
            ],
            'lng' => [
                'type' => Type::float(),
                'resolve' => fn (DeliveryPoint $root) => $root->lng,
            ],
            'region_id' => [
                'type' => Type::int(),
                'resolve' => fn (DeliveryPoint $root) => $root->region_id,
            ],
            'district_id' => [
                'type' => Type::int(),
                'resolve' => fn (DeliveryPoint $root) => $root->district_id,
            ],
            'mahalla_id' => [
                'type' => Type::int(),
                'resolve' => fn (DeliveryPoint $root) => $root->mahalla_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (DeliveryPoint $root) => $root->deleted_at?->timestamp,
            ],
            'region' => [
                'type' => GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (DeliveryPoint $root) => $root->region,
            ],
            'district' => [
                'type' => GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (DeliveryPoint $root) => $root->district,
            ],
            'mahalla' => [
                'type' => GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (DeliveryPoint $root) => $root->mahalla,
            ],
        ];
    }
}
