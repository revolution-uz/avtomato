<?php

namespace App\GraphQL\Common;

use App\GraphQL\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\Common\Queries\DistrictsGraphQLQuery;
use App\GraphQL\Common\Queries\MahallasGraphQLQuery;
use App\GraphQL\Common\Queries\RegionsGraphQLQuery;
use App\GraphQL\Common\Queries\SettingsGraphQLQuery;
use App\GraphQL\Common\Types\DistrictGraphQLType;
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
                SettingsGraphQLQuery::class,
                RegionsGraphQLQuery::class,
                DistrictsGraphQLQuery::class,
                MahallasGraphQLQuery::class,
            ],
            'mutation' => [
                LoginGraphQLMutation::class,
            ],
            'types' => [
                SettingsGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
