<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Models\Company\LoyaltyProgram\OrderPayment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderPaymentGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderPaymentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->id,
            ],
            'order_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->order_id,
            ],
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (OrderPayment $root) => $root->admin_id,
            ],
            'payment_method' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->payment_method,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->status->value,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (OrderPayment $root) => $root->amount,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderPayment $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (OrderPayment $root) => $root->deleted_at?->timestamp,
            ],
            'order' => [
                'type' => Type::nonNull(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderPayment $root) => $root->order,
            ],
            'admin' => [
                'type' => GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (OrderPayment $root) => $root->admin,
            ],
        ];
    }
}
