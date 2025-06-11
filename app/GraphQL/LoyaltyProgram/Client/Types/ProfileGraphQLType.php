<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Models\Company\LoyaltyProgram\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProfileGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProfileGraphQLType';

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
            'distributed_at' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->distributed_at?->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->updated_at->timestamp,
            ],
            'subscriptions' => [
                'type' => Type::listOf(GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->subscriptions()->where('is_active', true)->get(),
            ],
            'qr' => [
                'type' => GraphQL::type(QrGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (User $root) => $root->getQR(),
            ],
            'distributor_qr' => [
                'type' => GraphQL::type(QrGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (User $root) => $root->getQR(isDistributed: true),
            ],
            'cards' => [
                'type' => Type::listOf(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->cards()->active()->get(),
            ],
            'distributor' => [
                'type' => GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (User $root) => $root->distributor,
            ],
            'recipient' => [
                'type' => GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (User $root) => $root->recipient,
            ],
        ];
    }
}
