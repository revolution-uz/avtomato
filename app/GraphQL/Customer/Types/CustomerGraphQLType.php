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
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Customer $root) {
                    return $root->id;
                },
            ],
            'pinfl' => [
                'type' => Type::int(),
                'resolve' => function (Customer $root) {
                    return $root->pinfl;
                },
            ],
            'tin' => [
                'type' => Type::int(),
                'resolve' => function (Customer $root) {
                    return $root->tin;
                },
            ],
            'passport_series' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_series;
                },
            ],
            'passport_number' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_number;
                },
            ],
            'passport_given_date' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_given_date?->format('d-m-Y');
                },
            ],
            'passport_expiration_date' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_expiration_date?->format('d-m-Y');
                },
            ],
            'passport_given_place' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_given_place;
                },
            ],
            'passport_citizenship' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->passport_citizenship;
                },
            ],
            'first_name' => [
                'type' => Type::string(),
                'description' => 'Firstname of customer',
                'resolve' => function (Customer $root) {
                    return $root->first_name;
                },
            ],
            'last_name' => [
                'type' => Type::string(),
                'description' => 'Lastname of customer',
                'resolve' => function (Customer $root) {
                    return $root->last_name;
                },
            ],
            'middle_name' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->middle_name;
                },
            ],
            'birth_date' => [
                'type' => Type::string(),
                'resolve' => function (Customer $root) {
                    return $root->birth_date?->format('d-m-Y');
                },
            ],
            'birth_place' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->middle_name,
            ],
            'gender' => [
                'type' => Type::int(),
                'resolve' => fn (Customer $root) => $root->gender,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Customer $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (Customer $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => function (Customer $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
