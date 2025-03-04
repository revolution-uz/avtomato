<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\Models\Company\LoyaltyProgram\Subscription;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SubscriptionGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SubscriptionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->id,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Subscription $root) => $root->is_active,
            ],
            'card_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->card_id,
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->tariff_id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->user_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->updated_at->timestamp,
            ],
            'card' => [
                'type' => GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Subscription $root) => $root->card,
            ],
            'tariff' => [
                'type' => GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Subscription $root) => $root->tariff,
            ],
            'user' => [
                'type' => GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Subscription $root) => $root->user,
            ],
        ];
    }
}
