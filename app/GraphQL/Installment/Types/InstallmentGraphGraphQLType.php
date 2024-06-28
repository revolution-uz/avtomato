<?php

namespace App\GraphQL\Installment\Types;

use App\Models\Company\InstallmentGraph;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentGraphGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentGraphGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGraph $root) => $root->id,
            ],
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (InstallmentGraph $root) => $root->installment_id,
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (InstallmentGraph $root) => $root->installment_id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (InstallmentGraph $root) => $root->amount,
            ],
            'paid_amount' => [
                'type' => Type::float(),
                'resolve' => fn (InstallmentGraph $root) => $root->paid_amount,
            ],
            'overdue_days' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraph $root) => $root->overdue_days,
            ],
            'status' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraph $root) => $root->status->value,
            ],
            'starts_in' => [
                'type' => Type::int(),
                'resolve' => fn (InstallmentGraph $root) => $root->starts_in?->timestamp,
            ],
        ];
    }
}
