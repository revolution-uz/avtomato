<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\LoyaltyProgram\Card;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

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
            'pan' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Card $root) => $root->pan,
            ],
            'owner' => [
                'type' => Type::string(),
                'resolve' => fn (Card $root) => $root->owner,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (Card $root) => $root->phone,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->status->value,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->user_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Card $root) => $root->updated_at->timestamp,
            ],
            'card_type' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Card $root) => $root->card_type,
            ],
            'is_connected' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Card $root) => $root->is_connected,
            ],
            'user' => [
                'type' => Type::nonNull(GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Card $root) => $root->user,
            ],
        ];
    }
}
