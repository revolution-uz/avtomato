<?php

namespace App\GraphQL\Scoring\Types\Card;

use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardScoringResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CardScoringResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'report' => [
                'type' => Type::listOf(Type::nonNull(GraphQL::type(CardMonthlyAnalyticsGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => function (CardScoringResponseDTO $root) {
                    return $root->report;
                },
            ],
        ];
    }
}
