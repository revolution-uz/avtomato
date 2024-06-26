<?php

namespace App\GraphQL\Scoring\Types\Card;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardScoringRequestGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CardScoringRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(Type::string()),
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }
}
