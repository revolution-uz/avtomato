<?php

namespace App\GraphQL\Management\Types;

use App\GraphQL\Scalars\BigFloat;
use App\Models\Company\Installment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => Installment::class,
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
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->status->value,
            ],
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->branch_id,
            ],
            'admin_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->admin_id,
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->tariff_id,
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->type->value,
            ],
            'initial_fee' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
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
                'resolve' => fn (Installment $root) => $root->nextPaymentDate->timestamp,
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
            'debt' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Installment $root) => $root->debtAmount,
            ],
            'monthly_payment' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Installment $root) => $root->monthlyPayment,
            ],
        ];
    }
}
