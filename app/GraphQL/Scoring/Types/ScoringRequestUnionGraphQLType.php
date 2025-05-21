<?php

namespace App\GraphQL\Scoring\Types;

use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\CreditHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\PensionInfoGraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\UnionType;

class ScoringRequestUnionGraphQLType extends UnionType
{
    public const string GRAPHQL_TYPE_NAME = 'ScoringRequestUnionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Union of all scoring types',
    ];

    public function types(): array
    {
        return [
            GraphQL::type(CreditHistoryGraphQLType::GRAPHQL_TYPE_NAME),
            GraphQL::type(InpsHistoryGraphQLType::GRAPHQL_TYPE_NAME),
            GraphQL::type(PensionInfoGraphQLType::GRAPHQL_TYPE_NAME),
            GraphQL::type(CardScoringGraphQLType::GRAPHQL_TYPE_NAME),
            GraphQL::type(DebtScoringGraphQLType::GRAPHQL_TYPE_NAME),
        ];
    }

    public function resolveType($value)
    {
        if ($value instanceof CreditHistoryGraphQLType) {
            return GraphQL::type(CreditHistoryGraphQLType::GRAPHQL_TYPE_NAME);
        } elseif ($value instanceof InpsHistoryGraphQLType) {
            return GraphQL::type(InpsHistoryGraphQLType::GRAPHQL_TYPE_NAME);
        } elseif ($value instanceof PensionInfoGraphQLType) {
            return GraphQL::type(PensionInfoGraphQLType::GRAPHQL_TYPE_NAME);
        } elseif ($value instanceof CardScoringGraphQLType) {
            return GraphQL::type(CardScoringGraphQLType::GRAPHQL_TYPE_NAME);
        } elseif ($value instanceof DebtScoringGraphQLType) {
            return GraphQL::type(DebtScoringGraphQLType::GRAPHQL_TYPE_NAME);
        }
    }
}
