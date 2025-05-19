<?php

namespace App\GraphQL\Scoring\Types\Card;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardScoringRequestGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CardScoringRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pan' => [
                'name' => 'pan',
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn ($root) => substr_replace($root->number, '******', 6, 6),
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }
}
