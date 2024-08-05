<?php

namespace App\GraphQL\Installment\Types;

use App\Enums\Company\TransactionAttemptTypeEnum;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\GraphQL\Scalars\BigFloat;
use App\Models\Company\TransactionAttempt;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TransactionAttemptGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'TransactionAttemptGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->id,
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->type->value,
            ],
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->installment->branch_id,
            ],
            'branch' => [
                'type' => Type::nonNull(GraphQL::type(BranchGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (TransactionAttempt $root) => $root->installment->branch,
            ],
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->installment_id,
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (TransactionAttempt $root) => $root->installment,
            ],
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->customer_id,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (TransactionAttempt $root) => $root->customer,
            ],
            'transaction_id' => [
                'type' => Type::int(),
                'resolve' => fn (TransactionAttempt $root) => $root->transaction_id,
            ],
            'transaction' => [
                'type' => GraphQL::type(TransactionGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (TransactionAttempt $root) => $root->transaction,
            ],
            'card_id' => [
                'type' => Type::int(),
                'resolve' => fn (TransactionAttempt $root) => $root->card_id,
            ],
            'card' => [
                'type' => GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (TransactionAttempt $root) => $root->card,
            ],
            'card_pan' => [
                'type' => Type::string(),
                'resolve' => fn (TransactionAttempt $root) => $root->type == TransactionAttemptTypeEnum::ATMOS_PAY_WITH_CARD ? $root->card->pan : $root->transaction->card_pan,
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (TransactionAttempt $root) => $root->amount,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->updated_at->timestamp,
            ],
            'customer_type' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TransactionAttempt $root) => $root->customer_type,
            ],
            'transaction_type_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionAttempt $root) => $root->transaction_type_id,
            ],
            'transaction_type' => [
                'type' => Type::nonNull(GraphQL::type(TransactionTypeGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (TransactionAttempt $root) => $root->transactionType,
            ],
        ];
    }
}
