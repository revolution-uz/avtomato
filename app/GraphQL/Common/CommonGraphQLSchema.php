<?php

namespace App\GraphQL\Common;

use App\GraphQL\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\Common\Queries\CompanySettingsGraphQLQuery;
use App\GraphQL\Common\Queries\DashboardGraphQLQuery;
use App\GraphQL\Common\Queries\DistrictsGraphQLQuery;
use App\GraphQL\Common\Queries\MahallasGraphQLQuery;
use App\GraphQL\Common\Queries\RegionsGraphQLQuery;
use App\GraphQL\Common\Queries\SettingsGraphQLQuery;
use App\GraphQL\Common\Types\ChartItemGraphQLType;
use App\GraphQL\Common\Types\CompanySettingsGraphQLType;
use App\GraphQL\Common\Types\DashboardGraphQLType;
use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\InstallmentAmountsByIncreasingValueGraphQLType;
use App\GraphQL\Common\Types\InstallmentMonthlyInfoGraphQLType;
use App\GraphQL\Common\Types\InstallmentNplByIncreasingValueGraphQLType;
use App\GraphQL\Common\Types\InstallmentOverdueGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Common\Types\SettingsGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CompanySettingsGraphQLQuery::class,
                SettingsGraphQLQuery::class,
                RegionsGraphQLQuery::class,
                DistrictsGraphQLQuery::class,
                MahallasGraphQLQuery::class,
                DashboardGraphQLQuery::class,
            ],
            'mutation' => [
                LoginGraphQLMutation::class,
            ],
            'types' => [
                CompanySettingsGraphQLType::class,
                SettingsGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                InstallmentMonthlyInfoGraphQLType::class,
                DashboardGraphQLType::class,
                InstallmentOverdueGraphQLType::class,
                InstallmentAmountsByIncreasingValueGraphQLType::class,
                InstallmentNplByIncreasingValueGraphQLType::class,
                ChartItemGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
