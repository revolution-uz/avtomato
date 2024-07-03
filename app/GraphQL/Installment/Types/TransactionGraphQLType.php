<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\Models\Company\Transaction;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TransactionGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'TransactionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->id,
            ],
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->installment->branch_id,
            ],
            'branch' => [
                'type' => Type::nonNull(GraphQL::type(BranchGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Transaction $root) => $root->installment->branch,
            ],
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (Transaction $root) => $root->employee_id,
            ],
            'admin' => [
                'type' => GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Transaction $root) => $root->employee,
            ],
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->installment_id,
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Transaction $root) => $root->installment_id,
            ],
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->customer_id,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Transaction $root) => $root->customer,
            ],
            'transaction_type_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->transaction_type_id,
            ],
            'transaction_type' => [
                'type' => Type::nonNull(GraphQL::type(TransactionTypeGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Transaction $root) => $root->transactionType,
            ],
            'card_id' => [
                'type' => Type::int(),
                'resolve' => fn (Transaction $root) => $root->card_id,
            ],
            'card' => [
                'type' => GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Transaction $root) => $root->card,
            ],
            'external_id' => [
                'type' => Type::string(),
                'resolve' => fn (Transaction $root) => $root->external_id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Transaction $root) => $root->amount,
            ],
            'paid_at' => [
                'type' => Type::int(),
                'resolve' => fn (Transaction $root) => $root->paid_at?->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Transaction $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
