<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\GraphQL\Scalars\BigFloat;
use App\Models\Company\Installment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StatisticsInfoGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'StatisticsInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->id,
            ],
            'debt' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->debtAmount,
            ],
            'paid' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->transactions_sum_amount ?? 0,
            ],
            'branch' => [
                'type' => Type::nonNull(GraphQL::type(BranchGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->branch,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->customer,
            ],
            'transaction' => [
                'type' => GraphQL::type(TransactionGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Installment $root) => $root->transactions()->first(),
            ],
        ];
    }
}
