<?php

namespace App\GraphQL\Installment\Types;

use App\DTOs\Installment\InstallmentGraphDTO;
use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentGraphGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentGraphGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->id,
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->amount,
            ],
            'paid_amount' => [
                'type' => GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->paid_amount,
            ],
            'overdue_days' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->overdue_days,
            ],
            'status' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->status->value,
            ],
            'status_name' => [
                'type' => Type::string(),
                'resolve' => fn (InstallmentGraphDTO $root) => __('api.installment_status_' . $root->status->name),
            ],
            'starts_in' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraphDTO $root) => $root->starts_in->timestamp,
            ],
        ];
    }
}
