<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryInputDTO;
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
                'resolve' => function (RoyxatCreditHistoryInputDTO $root) {
                    return $root->passport_id;
                },
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => function (RoyxatCreditHistoryInputDTO $root) {
                    return $root->passport_id;
                },
            ],
            'tin' => [
                'type' => Type::string(),
                'resolve' => function (RoyxatCreditHistoryInputDTO $root) {
                    return $root->passport_id;
                },
            ],
        ];
    }
}
