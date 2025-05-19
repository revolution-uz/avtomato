<?php

namespace App\GraphQL\Scoring\Types\Card;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CreateCardScoringGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CreateCardScoringGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'send_otp' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (array $root) => $root['send_otp'],
            ],
            'card_id' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['card_id'],
            ],
            'phone_mask' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['phone_mask'],
            ],
            'scoring_id' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['scoring_id'],
            ],
        ];
    }
}
