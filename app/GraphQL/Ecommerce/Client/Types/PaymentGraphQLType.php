<?php

namespace App\GraphQL\Ecommerce\Client\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\Payment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaymentGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'PaymentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Payment $root) => $root->amount,
            ],
            'card_pan' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Payment $root) => $root->card_pan,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->status->value,
            ],
            'paid_at' => [
                'type' => Type::int(),
                'resolve' => fn (Payment $root) => $root->paid_at?->timestamp,
            ],
            'external_id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Payment $root) => $root->external_id,
            ],
            'order_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->order_id,
            ],
            'transaction_type_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->transaction_type_id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->user_id,
            ],
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (Payment $root) => $root->admin_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Payment $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
