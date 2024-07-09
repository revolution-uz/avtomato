<?php

namespace App\GraphQL\Customer\Types;

use App\DTOs\PassportDataDTO;
use App\Enums\GenderEnum;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'passport_series' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Series',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->series;
                },
            ],
            'passport_number' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Number',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->number;
                },
            ],
            'passport_given_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Given date',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->given_date->format('Y-m-d');
                },
            ],
            'passport_expiration_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Expiration date',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->expiration_date->format('Y-m-d');
                },
            ],
            'passport_given_place' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Given place',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->given_place;
                },
            ],
            'passport_given_org' => [
                'type' => Type::string(),
                'description' => 'Passport Given org',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->given_org;
                },
            ],
            'passport_citizenship_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Passport Citizenship ID',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->citizenship_id;
                },
            ],
            'passport_citizenship' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport citizenship',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->citizenship;
                },
            ],
            'passport_living_place' => [
                'type' => Type::string(),
                'description' => 'Passport Living place',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->living_place;
                },
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'First Name',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->name;
                },
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Last Name',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->sur_name;
                },
            ],
            'middle_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Middle Name',
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
            'birth_place' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Birth place',
                'resolve' => function (PassportDataDTO $root) {
                    return $root->birth_place;
                },
            ],
            'gender' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'gender',
                'resolve' => function (PassportDataDTO $root) {
                    return GenderEnum::convertFromOilaKredit($root->gender)->value; // OilaKredit gender same as our
                },
            ],
            'address' => [
                'type' => GraphQL::type(CustomerAddressByPassportDataGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Address',
                'resolve' => function (PassportDataDTO $root) {
                    return $root;
                },
            ],
        ];
    }
}
