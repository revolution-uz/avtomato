<?php

namespace App\GraphQL\Installment;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Installment\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\CreateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\Installment\Mutations\UpdateTransactionTypeGraphQLMutation;
use App\GraphQL\Installment\Queries\InstallmentsGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleInstallmentGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionGraphQLQuery;
use App\GraphQL\Installment\Queries\SingleTransactionTypeGraphQLQuery;
use App\GraphQL\Installment\Queries\TariffsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionsGraphQLQuery;
use App\GraphQL\Installment\Queries\TransactionTypesGraphQLQuery;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\GraphQL\Installment\Types\TariffInputGraphQLType;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class InstallmentGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                InstallmentsGraphQLQuery::class,
                SingleInstallmentGraphQLQuery::class,
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                TransactionTypesGraphQLQuery::class,
                SingleTransactionTypeGraphQLQuery::class,
                TransactionsGraphQLQuery::class,
                SingleTransactionGraphQLQuery::class
            ],
            'mutation' => [
                CreateTariffGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                CreateTransactionTypeGraphQLMutation::class,
                UpdateTransactionTypeGraphQLMutation::class,
            ],
            'types' => [
                InstallmentGraphQLType::class,
                InstallmentGraphGraphQLType::class,
                TariffInputGraphQLType::class,
                TariffGraphQLType::class,
                TransactionTypeInputGraphQLType::class,
                TransactionTypeGraphQLType::class,
                CustomerGraphQLType::class,
                TransactionGraphQLType::class
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
