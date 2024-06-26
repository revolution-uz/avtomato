<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\Models\Company\Installment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentGraphQLType';

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
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->customer_id,
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->customer,
            ],
            'graphs' => [
                'type' => Type::listOf(GraphQL::type(InstallmentGraphGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->graphs,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->status->value,
            ],
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->branch_id,
            ],
            'branch' => [
                'type' => Type::nonNull(GraphQL::type(BranchGraphQLType::class)),
                'resolve' => fn (Installment $root) => $root->branch,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->user_id,
            ],
            'user' => [
                'type' => Type::nonNull(GraphQL::type(AdminGraphQLType::class)),
                'resolve' => fn (Installment $root) => $root->user,
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->tariff_id,
            ],
            'tariff' => [
                'type' => Type::nonNull(GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Installment $root) => $root->tariff,
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->type->value,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Installment $root) => $root->amount,
            ],
            'initial_fee' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Installment $root) => $root->initial_fee,
            ],
            'sms_notify' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Installment $root) => $root->sms_notify,
            ],
            'auto_pay' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Installment $root) => $root->auto_pay,
            ],
            'auto_pay_by_pinfl' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Installment $root) => $root->any_card_charge,
            ],
            'is_blacklist' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Installment $root) => $root->is_blacklist,
            ],
            'period' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->period,
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => fn (Installment $root) => $root->description,
            ],
            'cancellation_details' => [
                'type' => Type::string(),
                'resolve' => fn (Installment $root) => $root->cancellation_details,
            ],
            'starts_in' => [
                'type' => Type::int(),
                'resolve' => fn (Installment $root) => $root->starts_in?->timestamp,
            ],
            'next_pay_date' => [
                'type' => Type::int(),
                'resolve' => fn (Installment $root) => $root->next_pay_date?->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->updated_at->timestamp,
            ],
            'canceled_at' => [
                'type' => Type::int(),
                'resolve' => fn (Installment $root) => $root->canceled_at?->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Installment $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
