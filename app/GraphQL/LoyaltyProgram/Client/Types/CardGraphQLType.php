<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\LoyaltyProgram\Card;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->user_id,
            ],
            'user' => [
                'type' => Type::nonNull(GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Card $root) => $root->user,
            ],
            'number' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Card $root) => $root->pan,
            ],
            'card_type' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Card $root) => $root->card_type,
            ],
            'owner' => [
                'type' => Type::string(),
                'resolve' => fn (Card $root) => $root->owner,
            ],
            'expiry' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Card $root) => $root->expiry,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (Card $root) => $root->phone,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->status->value,
            ],
            'balance' => [
                'type' => Type::float(),
                'resolve' => fn (Card $root) => $root->balance,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
