<?php

namespace App\GraphQL\Installment;

use App\GraphQL\Installment\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionTypeGraphQLQuery;
use App\GraphQL\Installment\Queries\TariffsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionTypesGraphQLQuery;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\GraphQL\Installment\Types\TariffInputGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class InstallmentGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                TransactionTypesGraphQLQuery::class,
                SingleTransactionTypeGraphQLQuery::class,
            ],
            'mutation' => [
                CreateTariffGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                CreateTransactionTypeGraphQLMutation::class,
                UpdateTransactionTypeGraphQLMutation::class,
            ],
            'types' => [
                TariffInputGraphQLType::class,
                TariffGraphQLType::class,
                TransactionTypeInputGraphQLType::class,
                TransactionTypeGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
