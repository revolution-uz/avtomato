<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

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
            'remaining_limit_amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->remaining_limit_amount,
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->tariff_id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->user_id,
            ],
            'expired_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->expired_at->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Subscription $root) => $root->updated_at->timestamp,
            ],
            'tariff' => [
                'type' => GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Subscription $root) => $root->tariff,
            ],
            'user' => [
                'type' => GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Subscription $root) => $root->user,
            ],
            'is_available' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Subscription $root) => $root->is_available,
            ],
        ];
    }
}
