<?php

namespace App\GraphQL\Scoring;

use App\GraphQL\Scoring\Queries\KatmCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadKatmCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Types\CreditHistory\CreditScoringGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryAdditionalInfoGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryDebtGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryScoreGraphQLType;
use App\GraphQL\Scoring\Types\KatmClientGraphQLType;
use App\GraphQL\Scoring\Types\KatmCreateRequestGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ScoringGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                KatmCreditHistoryGraphQLQuery::class,
                ReadKatmCreditHistoryGraphQLQuery::class,
            ],
            'mutation' => [
            ],
            'types' => [
                KatmClientGraphQLType::class,
                KatmCreateRequestGraphQLType::class,
                CreditScoringGraphQLType::class,
                KatmCreditHistoryAdditionalInfoGraphQLType::class,
                KatmCreditHistoryDebtGraphQLType::class,
                KatmCreditHistoryResponseGraphQLType::class,
                KatmCreditHistoryScoreGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
