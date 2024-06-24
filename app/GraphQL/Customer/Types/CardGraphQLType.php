<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Card;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CardGraphQLType';

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
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->customer_id,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Card $root) => $root->customer,
            ],
            'number' => [
                'type' => Type::nonNull(Type::string()),
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
            'is_connected' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Card $root) => $root->is_connected,
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Card $root) => $root->phone,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->status,
            ],
            'balance' => [
                'type' => Type::nonNull(Type::float()),
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
