<?php

namespace App\GraphQL\QrPay;

use App\GraphQL\QrPay\Mutations\CreateTransactionGraphQLMutation;
use App\GraphQL\QrPay\Queries\SingleTransactionGraphQLQuery;
use App\GraphQL\QrPay\Queries\TransactionsGraphQLQuery;
use App\GraphQL\QrPay\Types\SbpPaymentCreditGraphQLType;
use App\GraphQL\QrPay\Types\SbpPaymentDebitGraphQLType;
use App\GraphQL\QrPay\Types\SbpPaymentReceiverGraphQLType;
use App\GraphQL\QrPay\Types\SbpPaymentSenderGraphQLType;
use App\GraphQL\QrPay\Types\TransactionGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class QrPayGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                TransactionsGraphQLQuery::class,
                SingleTransactionGraphQLQuery::class,
            ],
            'mutation' => [
                CreateTransactionGraphQLMutation::class,
            ],
            'types' => [
                TransactionGraphQLType::class,
                SbpPaymentCreditGraphQLType::class,
                SbpPaymentDebitGraphQLType::class,
                SbpPaymentSenderGraphQLType::class,
                SbpPaymentReceiverGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'qr_pay', 'tenant_logging'],
        ];
    }
}
