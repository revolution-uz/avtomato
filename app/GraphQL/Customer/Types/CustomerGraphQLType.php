<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Customer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CustomerGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CustomerGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Customer GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of customer',
                'resolve' => function (Customer $root) {
                    return $root->id;
                },
            ],
            'pinfl' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'PINFL of customer',
                'resolve' => function (Customer $root) {
                    return $root->pinfl;
                },
            ],
            'tin' => [
                'type' => Type::int(),
                'description' => 'TIN of customer',
                'resolve' => function (Customer $root) {
                    return $root->tin;
                },
            ],
            'passport_series' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport Series of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_series;
                },
            ],
            'passport_number' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport number of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_number;
                },
            ],
            'passport_given_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport given date of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_given_date?->timestamp;
                },
            ],
            'passport_expiration_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport expiration date of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_expiration_date?->timestamp;
                },
            ],
            'passport_given_place' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport given place of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_given_place;
                },
            ],
            'passport_citizenship' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Passport citizenship of customer',
                'resolve' => function (Customer $root) {
                    return $root->passport_citizenship;
                },
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Firstname of customer',
                'resolve' => function (Customer $root) {
                    return $root->first_name;
                },
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Lastname of customer',
                'resolve' => function (Customer $root) {
                    return $root->last_name;
                },
            ],
            'middle_name' => [
                'type' => Type::string(),
                'description' => 'Middle name of customer',
                'resolve' => function (Customer $root) {
                    return $root->last_name;
                },
            ],
            'full_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Full name of customer',
                'resolve' => function (Customer $root) {
                    return $root->full_name;
                },
            ],
            'gender' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Gender of customer',
                'resolve' => function (Customer $root) {
                    return $root->gender;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Customer created timestamp',
                'resolve' => function (Customer $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Customer updated timestamp',
                'resolve' => function (Customer $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Customer deleted timestamp',
                'resolve' => function (Customer $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
