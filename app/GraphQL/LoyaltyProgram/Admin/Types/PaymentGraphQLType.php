<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Models\Company\LoyaltyProgram\LoyaltyProgramPayment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaymentGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'PaymentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->amount,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->status->value,
            ],
            'paid_at' => [
                'type' => Type::int(),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->paid_at?->timestamp,
            ],
            'card_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->card_id,
            ],
            'subscription_id' => [
                'type' => Type::int(),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->subscription_id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->user_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->updated_at->timestamp,
            ],
            'user' => [
                'type' => Type::nonNull(GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->user,
            ],
            'subscription' => [
                'type' => GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (LoyaltyProgramPayment $root) => $root->subscription,
            ],
        ];
    }
}
