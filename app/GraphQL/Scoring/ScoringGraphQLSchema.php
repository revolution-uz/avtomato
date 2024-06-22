<?php

namespace App\GraphQL\Scoring;

use App\GraphQL\Scoring\Queries\CreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\PensionInfoGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadCreditHistoryGraphQLQuery;
use App\GraphQL\Scoring\Queries\ReadPensionInfoGraphQLQuery;
use App\GraphQL\Scoring\Types\CreditHistory\CreditScoringGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryAdditionalInfoGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryDebtGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryResponseGraphQLType;
use App\GraphQL\Scoring\Types\CreditHistory\KatmCreditHistoryScoreGraphQLType;
use App\GraphQL\Scoring\Types\KatmClientGraphQLType;
use App\GraphQL\Scoring\Types\KatmCreateRequestGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\KatmPensionDataGraphQLType;
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
                PensionInfoGraphQLQuery::class,
                ReadPensionInfoGraphQLQuery::class,
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
                KatmPensionDataGraphQLType::class,
                KatmPensionRowGraphQLType::class,
                KatmPensionSizePensionGraphQLType::class,
                PensionInfoGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
