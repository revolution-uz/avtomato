<?php

namespace App\GraphQL\Customer\Types;

use App\DTOs\PassportDataDTO;
use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\Models\Company\District;
use App\Models\Company\Region;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CustomerAddressByPassportDataGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CustomerAddressByPassportDataGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Customer Address By Passport Data GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'address' => [
                'type' => Type::string(),
                'description' => 'Address',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->address;
                },
            ],
            'region_id' => [
                'type' => Type::int(),
                'description' => 'Region ID',
                'resolve' => function (PassportDataDTO $root) {
                    $region = Region::where('oilakredit_id', $root->region_id)->first();

                    return $region?->id;
                },
            ],
            'region' => [
                'type' => GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Region ID',
                'resolve' => function (PassportDataDTO $root) {
                    return Region::where('oilakredit_id', $root->region_id)->first();
                },
            ],
            'district_id' => [
                'type' => Type::int(),
                'description' => 'District ID',
                'resolve' => function (PassportDataDTO $root) {
                    $district = District::where('oilakredit_id', $root->district_id)->first();

                    return $district?->id;
                },
            ],
            'district' => [
                'type' => GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'District',
                'resolve' => function (PassportDataDTO $root) {
                    return District::where('oilakredit_id', $root->district_id)->first();
                },
            ],
            'mahalla_id' => [
                'type' => Type::int(),
                'description' => 'Mahalla ID',
                'resolve' => function (PassportDataDTO $root) {
                    return null;
                },
            ],
            'mahalla' => [
                'type' => GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Mahalla',
                'resolve' => function (PassportDataDTO $root) {
                    return null;
                },
            ],
        ];
    }
}
