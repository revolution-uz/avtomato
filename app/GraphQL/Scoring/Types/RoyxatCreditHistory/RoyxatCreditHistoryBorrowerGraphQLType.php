<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryBorrowerDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryBorrowerGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryBorrowerGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditHistoryBorrowerDTO::class,
    ];

    public function fields(): array
    {
        return [
            'full_name' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryBorrowerDTO $root) => $root->full_name,
            ],
            'passport_id' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryBorrowerDTO $root) => $root->passport_id,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryBorrowerDTO $root) => $root->pinfl,
            ],
            'birth_date' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryBorrowerDTO $root) => $root->birth_date,
            ],
        ];
    }
}
