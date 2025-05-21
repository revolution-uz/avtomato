<?php

namespace App\GraphQL\Customer\Types;

use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestUnionGraphQLType;
use App\Models\Company\Customer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'unired_cards_last_updated_at' => [
                'type' => Type::int(),
                'resolve' => fn (Customer $root) => $root->unired_cards_last_updated_at?->timestamp,
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
            'cards' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->cards,
            ],
            'unired_cards' => [
                'type' => Type::listOf(GraphQL::type(UniredCardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Customer $root) => $root->getUniredCards(),
            ],
            'addresses' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->addresses,
            ],
            'phones' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(PhoneGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->phones,
            ],
            'installments' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->installments,
            ],
            'scorings' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(ScoringRequestGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->scoringRequests,
            ],
            'scoring_requests' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(ScoringRequestUnionGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->scoringRequests,
            ],
            'tags' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->tags,
            ],
        ];
    }
}
