<?php

namespace App\GraphQL\Scoring;

use App\GraphQL\Scoring\Queries\CreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\InpsHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\PensionInfoGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadInpsHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadPensionInfoGraphQLQuery;
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
                CreditHistoryGraphQLQuery::class,
                ReadCreditHistoryGraphQLQuery::class,
                InpsHistoryGraphQLQuery::class,
                ReadInpsHistoryGraphQLQuery::class,
                PensionInfoGraphQLQuery::class,
                ReadPensionInfoGraphQLQuery::class,
            ],
            'mutation' => [
            ],
            'types' => [
                CreditHistoryGraphQLType::class,
                KatmCreditHistoryResponseGraphQLType::class,
                KatmClientGraphQLType::class,
                KatmCreateRequestGraphQLType::class,
                KatmCreditHistoryAdditionalInfoGraphQLType::class,
                KatmCreditHistoryDebtGraphQLType::class,
                KatmCreditHistoryScoreGraphQLType::class,

                InpsHistoryGraphQLType::class,
                KatmInpsHistoryResponseGraphQLType::class,
                KatmIncomeGraphQLType::class,
                KatmIncomesGraphQLType::class,
                KatmIncomesPeriodGraphQLType::class,
                KatmPresenceReportGraphQLType::class,
                KatmPresenceReportsGraphQLType::class,

                PensionInfoGraphQLType::class,
                KatmPensionInfoResponseGraphQLType::class,
                KatmPensionDataGraphQLType::class,
                KatmPensionRowGraphQLType::class,
                KatmPensionSizePensionGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
