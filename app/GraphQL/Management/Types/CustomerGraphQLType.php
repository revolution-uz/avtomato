<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\Customer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CustomerGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CustomerGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => Customer::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Customer $root) => $root->id,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->pinfl,
            ],
            'tin' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->tin,
            ],
            'passport_series' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_series,
            ],
            'passport_number' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_number,
            ],
            'passport_given_date' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_given_date?->timestamp,
            ],
            'passport_expiration_date' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_expiration_date?->timestamp,
            ],
            'passport_given_place' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_given_place,
            ],
            'passport_citizenship' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->passport_citizenship,
            ],
            'first_name' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->first_name,
            ],
            'last_name' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->last_name,
            ],
            'middle_name' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->middle_name,
            ],
            'birth_date' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->birth_date?->timestamp,
            ],
            'birth_place' => [
                'type' => Type::string(),
                'resolve' => fn (Customer $root) => $root->birth_place,
            ],
            'gender' => [
                'type' => Type::int(),
                'resolve' => fn (Customer $root) => $root->gender->value,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Customer $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Customer $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Customer $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
