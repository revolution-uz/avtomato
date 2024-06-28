<?php

namespace App\GraphQL\Customer\Types;

use App\DTOs\PassportDataDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PassportDataGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PassportDataGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Passport Data GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Pinfl',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->pinfl;
                },
            ],
            'tin' => [
                'type' => Type::string(),
                'description' => 'Tin',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->tin;
                },
            ],
            'series' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Series',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->series;
                },
            ],
            'number' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Number',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->number;
                },
            ],
            'sur_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Surname',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->sur_name;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->name;
                },
            ],
            'patronymic_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Patronymic name',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->patronymic_name;
                },
            ],
            'full_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Full name',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->full_name;
                },
            ],
            'birth_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Birth date',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->birth_date->format('Y-m-d');
                },
            ],
            'expiration_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Expiration date',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->expiration_date->format('Y-m-d');
                },
            ],
            'given_place' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Given place',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->given_place;
                },
            ],
            'given_org' => [
                'type' => Type::string(),
                'description' => 'Given org',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->given_org;
                },
            ],
            'living_place' => [
                'type' => Type::string(),
                'description' => 'Living place',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->living_place;
                },
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Address',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->address;
                },
            ],
            'region_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Region ID',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->region_id;
                },
            ],
            'district_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'District ID',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->district_id;
                },
            ],
            'gender' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'gender',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->gender;
                },
            ],
            'citizenship_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Citizenship ID',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->citizenship_id;
                },
            ],
            'citizenship' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Citizenship',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->citizenship;
                },
            ],
        ];
    }
}
