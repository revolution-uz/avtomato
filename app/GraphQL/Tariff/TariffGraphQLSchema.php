<?php

namespace App\GraphQL\Tariff;

use App\GraphQL\Tariff\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\Tariff\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\Tariff\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Tariff\Queries\TariffsGraphQLQuery;
use App\GraphQL\Tariff\Types\TariffGraphQLType;
use App\GraphQL\Tariff\Types\TariffInputGraphQLType;
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
                CreateTariffGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
            ],
            'types' => [
                TariffInputGraphQLType::class,
                TariffGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
