<?php

namespace App\GraphQL\Info;

use App\GraphQL\Info\Queries\DistrictsGraphQLQuery;
use App\GraphQL\Info\Queries\MahallasGraphQLQuery;
use App\GraphQL\Info\Queries\RegionsGraphQLQuery;
use App\GraphQL\Info\Types\DistrictGraphQLType;
use App\GraphQL\Info\Types\MahallaGraphQLType;
use App\GraphQL\Info\Types\RegionGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class InfoQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                RegionsGraphQLQuery::class,
                DistrictsGraphQLQuery::class,
                MahallasGraphQLQuery::class,
            ],
            'mutation' => [
            ],
            'types' => [
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
