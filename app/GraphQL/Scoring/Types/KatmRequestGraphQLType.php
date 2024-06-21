<?php

namespace App\GraphQL\Scoring\Types;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmRequestGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'ScoringRequestDTOGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Scoring Request DTO GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'PINFL of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->pinfl;
                },
            ],
            'passport_series' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport series of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->passport_series;
                },
            ],
            'passport_number' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport number of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->passport_number;
                },
            ],
            'passport_given_date' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Passport given date of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->passport_given_date->timestamp;
                },
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'First name of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->first_name;
                },
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Last name of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->last_name;
                },
            ],
            'middle_name' => [
                'type' => Type::string(),
                'description' => 'Middle name of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->middle_name;
                },
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Phone of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->phone;
                },
            ],
            'extra_phone' => [
                'type' => Type::string(),
                'description' => 'Extra phone of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->extra_phone;
                },
            ],
            'birth_date' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Birth date of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->birth_date->timestamp;
                },
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Region ID of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->region_id;
                },
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'District ID of scoring request DTO',
                'resolve' => function (KatmCreateRequestDTO $root) {
                    return $root->district_id;
                },
            ],
        ];
    }
}
