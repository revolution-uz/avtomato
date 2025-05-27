<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\Models\Company\LoyaltyProgram\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'UserGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->id,
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (User $root) => $root->phone,
            ],
            'full_name' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->full_name,
            ],
            'first_name' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->first_name,
            ],
            'last_name' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->last_name,
            ],
            'birth_date' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->birth_date?->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->deleted_at?->timestamp,
            ],
            'subscriptions' => [
                'type' => Type::listOf(GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->subscriptions,
            ],
            'cards' => [
                'type' => Type::listOf(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->cards,
            ],
            'has_available_subscription' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (User $root) => $root->has_available_subscription,
            ],
        ];
    }
}
