<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditHistoryResponseDTO::class,
    ];

    public function fields(): array
    {
        return [
            'borrower' => [
                'type' => GraphQL::type(RoyxatCreditHistoryBorrowerGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->borrower,
            ],
            'loans' => [
                'type' => Type::listOf((GraphQL::type(RoyxatCreditHistoryLoanGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->loans,
            ],
        ];
    }
}
