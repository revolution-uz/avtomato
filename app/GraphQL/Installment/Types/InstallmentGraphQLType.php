<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
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
            'branch_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Installment $root) => $root->branch_id,
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
                'resolve' => fn (Installment $root) => $root->type,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Installment $root) => $root->amount,
            ],
            'initial_fee' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Installment $root) => $root->initial_fee,
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Installment $root) => $root->description,
            ],
            'sms_notify' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Installment $root) => $root->sms_notify,
            ],
        ];
    }
}
