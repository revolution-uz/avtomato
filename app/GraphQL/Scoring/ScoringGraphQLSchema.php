<?php

namespace App\GraphQL\Scoring;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Scoring\Mutations\CreateCardScoringGraphQLMutation;
use App\GraphQL\Scoring\Mutations\CreateDebtScoringGraphQLMutation;
use App\GraphQL\Scoring\Mutations\CreateKatmScoringGraphQLMutation;
use App\GraphQL\Scoring\Mutations\DeleteScoringRequestsGraphQLMutation;
use App\GraphQL\Scoring\Queries\CardScoringRequestsGraphQLQuery;
use App\GraphQL\Scoring\Queries\CreditHistoriesGraphQLQuery;
use App\GraphQL\Scoring\Queries\DebtScoringRequestsGraphQLQuery;
use App\GraphQL\Scoring\Queries\InpsHistoriesGraphQLQuery;
use App\GraphQL\Scoring\Queries\PensionInfosGraphQLQuery;
use App\GraphQL\Scoring\Queries\RoyxatCreditsGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleCardScoringRequestGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleDebtScoringRequestGraphQLQuery;
use App\GraphQL\Scoring\Queries\SingleInpsHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\SinglePensionInfoGraphQLQuery;
use App\GraphQL\Scoring\Types\Card\CardCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardMonthlyAnalyticsGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardMonthlyDetailedAnalyticsGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringResponseGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\CreditHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryAdditionalInfoGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryDebtGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\KatmCreditHistoryScoreGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomeGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomesGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmIncomesPeriodGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmInpsHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmPresenceReportGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\KatmPresenceReportsGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmClientGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionDataGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionInfoResponseGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionRowGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\KatmPensionSizePensionGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\PensionInfoGraphQLType;
use App\GraphQL\Scoring\Types\Partners\RoyxatCreditGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ScoringGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CardScoringRequestsGraphQLQuery::class,
                CreditHistoriesGraphQLQuery::class,
                DebtScoringRequestsGraphQLQuery::class,
                InpsHistoriesGraphQLQuery::class,
                PensionInfosGraphQLQuery::class,
                SingleCardScoringRequestGraphQLQuery::class,
                SingleCreditHistoryGraphQLQuery::class,
                SingleDebtScoringRequestGraphQLQuery::class,
                SingleInpsHistoryGraphQLQuery::class,
                SinglePensionInfoGraphQLQuery::class,
                RoyxatCreditsGraphQLQuery::class,
            ],
            'mutation' => [
                CreateCardScoringGraphQLMutation::class,
                CreateDebtScoringGraphQLMutation::class,
                CreateKatmScoringGraphQLMutation::class,
                DeleteScoringRequestsGraphQLMutation::class,
            ],
            'types' => [
                CardCreateInputGraphQLType::class,
                CardMonthlyAnalyticsGraphQLType::class,
                CardMonthlyDetailedAnalyticsGraphQLType::class,
                CardScoringGraphQLType::class,
                CardScoringRequestGraphQLType::class,
                CardScoringResponseGraphQLType::class,

                DebtCreateInputGraphQLType::class,
                DebtScoringGraphQLType::class,
                DebtScoringRequestGraphQLType::class,
                DebtScoringResponseGraphQLType::class,

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

                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,

                RoyxatCreditGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
