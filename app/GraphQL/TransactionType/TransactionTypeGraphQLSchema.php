<?php

namespace App\GraphQL\TransactionType;

use App\GraphQL\TransactionType\Mutations\CreateTransactionTypeGraphQLMutation;
use App\GraphQL\TransactionType\Mutations\UpdateTransactionTypeGraphQLMutation;
use App\GraphQL\TransactionType\Queries\SingleTransactionTypeGraphQLQuery;
use App\GraphQL\TransactionType\Queries\TransactionTypesGraphQLQuery;
use App\GraphQL\TransactionType\Types\TransactionTypeGraphQLType;
use App\GraphQL\TransactionType\Types\TransactionTypeInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class TransactionTypeGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                TransactionTypesGraphQLQuery::class,
                SingleTransactionTypeGraphQLQuery::class,
            ],
            'mutation' => [
                CreateTransactionTypeGraphQLMutation::class,
                UpdateTransactionTypeGraphQLMutation::class,
            ],
            'types' => [
                TransactionTypeInputGraphQLType::class,
                TransactionTypeGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
