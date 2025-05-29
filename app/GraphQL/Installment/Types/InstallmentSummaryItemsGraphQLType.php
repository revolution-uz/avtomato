<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigFloat;
use App\Models\Company\Installment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentSummaryItemsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentSummaryItemsGraphQLType';

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
            'customer_full_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Installment $root) => $root->customer->full_name,
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->amount,
            ],
            'installment_amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->installment_amount,
            ],
            'initial_fee' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->initial_fee,
            ],
            'debt' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->debt_amount,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->created_at->timestamp,
            ],
        ];
    }
}
