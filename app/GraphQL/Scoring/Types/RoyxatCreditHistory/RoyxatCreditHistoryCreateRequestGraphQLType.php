<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryRequestDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryCreateRequestGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryCreateRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'passport_id' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryRequestDTO $root) => $root->passport_id,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryRequestDTO $root) => $root->pinfl,
            ],
        ];
    }
}
