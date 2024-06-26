<?php

namespace App\GraphQL\Scoring\Types\Card;

use App\DTOs\Scoring\Card\MonthlyAnalyticsDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardMonthlyAnalyticsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CardMonthlyAnalyticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'month' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (MonthlyAnalyticsDTO $root) {
                    return $root->month->toDateString();
                },
            ],
            'full_amount' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (MonthlyAnalyticsDTO $root) {
                    return $root->full_amount;
                },
            ],
            'details' => [
                'type' => Type::listOf(Type::nonNull(GraphQL::type(CardMonthlyDetailedAnalyticsGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => function (MonthlyAnalyticsDTO $root) {
                    return $root->details;
                },
            ],
        ];
    }
}
