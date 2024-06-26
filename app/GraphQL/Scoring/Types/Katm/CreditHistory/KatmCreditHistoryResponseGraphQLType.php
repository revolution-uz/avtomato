<?php

namespace App\GraphQL\Scoring\Types\Katm\CreditHistory;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\GraphQL\Scoring\Types\Katm\KatmClientGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmCreditHistoryResponseGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmCreditHistoryResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Credit History Response GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'additional_info' => [
                'type' => Type::nonNull(GraphQL::type(KatmCreditHistoryAdditionalInfoGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Additional Info',
                'resolve' => function (KatmCreditHistoryResponseDTO $root) {
                    return $root->additional_info;
                },
            ],
            'score' => [
                'type' => Type::nonNull(GraphQL::type(KatmCreditHistoryScoreGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Score',
                'resolve' => function (KatmCreditHistoryResponseDTO $root) {
                    return $root->score;
                },
            ],
            'client' => [
                'type' => Type::nonNull(GraphQL::type(KatmClientGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Client',
                'resolve' => function (KatmCreditHistoryResponseDTO $root) {
                    return $root->client;
                },
            ],
        ];
    }
}
