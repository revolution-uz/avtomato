<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\LoyaltyProgram\Tariff;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActiveTariffGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ActiveTariffGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tariff $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tariff $root) => $root->name_ru,
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Tariff $root) => $root->amount,
            ],
            'free_days' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->free_days,
            ],
            'subscription_days' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->subscription_days,
            ],
            'is_free_for_previous_subscribers' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Tariff $root) => $root->is_free_for_previous_subscribers,
            ],
            'limit_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Tariff $root) => $root->limit_amount,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Tariff $root) => $root->is_active,
            ],
            'is_valid_for_free_trial' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Tariff $root) => $root->isValidForFreeTrial(auth('loyalty-program-client')->user()),
            ],
        ];
    }
}
