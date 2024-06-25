<?php

namespace App\GraphQL\Tariff;

use App\GraphQL\Tariff\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Tariff\Queries\TariffsGraphQLQuery;
use App\GraphQL\Tariff\Types\TariffGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class TariffGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
            ],
            'mutation' => [
            ],
            'types' => [
                TariffGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
