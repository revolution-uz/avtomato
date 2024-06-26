<?php

namespace App\GraphQL\Scoring;

use App\GraphQL\Scoring\Mutations\CreateKatmScoringGraphQLMutation;
use App\GraphQL\Scoring\Mutations\DeleteScoringRequestsGraphQLMutation;
use App\GraphQL\Scoring\Queries\CreditHistoriesGraphQLQuery;
use App\GraphQL\Scoring\Queries\InpsHistoriesGraphQLQuery;
use App\GraphQL\Scoring\Queries\PensionInfosGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleInpsHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\SinglePensionInfoGraphQLQuery;
use App\GraphQL\Scoring\Types\CreditHistory\CreditHistoryGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryAdditionalInfoGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryDebtGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryScoreGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\InpsHistoryGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmIncomeGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmIncomesGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmIncomesPeriodGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmInpsHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmPresenceReportGraphQLType;
use App\GraphQL\Scoring\Types\InpsHistory\KatmPresenceReportsGraphQLType;
use App\GraphQL\Scoring\Types\KatmClientGraphQLType;
use App\GraphQL\Scoring\Types\KatmCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\KatmCreateRequestGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\KatmPensionDataGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\KatmPensionInfoResponseGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\KatmPensionRowGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\KatmPensionSizePensionGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\PensionInfoGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ScoringGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CreditHistoriesGraphQLQuery::class,
                SingleCreditHistoryGraphQLQuery::class,
                InpsHistoriesGraphQLQuery::class,
                SingleInpsHistoryGraphQLQuery::class,
                PensionInfosGraphQLQuery::class,
                SinglePensionInfoGraphQLQuery::class,
            ],
            'mutation' => [
                CreateKatmScoringGraphQLMutation::class,
                DeleteScoringRequestsGraphQLMutation::class,
            ],
            'types' => [
                CreditHistoryGraphQLType::class,
                KatmCreditHistoryAdditionalInfoGraphQLType::class,
                KatmCreditHistoryDebtGraphQLType::class,
                KatmCreditHistoryResponseGraphQLType::class,
                KatmCreditHistoryScoreGraphQLType::class,

                KatmClientGraphQLType::class,
                KatmCreateInputGraphQLType::class,
                KatmCreateRequestGraphQLType::class,

                InpsHistoryGraphQLType::class,
                KatmIncomeGraphQLType::class,
                KatmIncomesGraphQLType::class,
                KatmIncomesPeriodGraphQLType::class,
                KatmInpsHistoryResponseGraphQLType::class,
                KatmPresenceReportGraphQLType::class,
                KatmPresenceReportsGraphQLType::class,

                KatmPensionDataGraphQLType::class,
                KatmPensionInfoResponseGraphQLType::class,
                KatmPensionRowGraphQLType::class,
                KatmPensionSizePensionGraphQLType::class,
                PensionInfoGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
