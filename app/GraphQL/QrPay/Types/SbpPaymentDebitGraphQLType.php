<?php

namespace App\GraphQL\QrPay\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SbpPaymentDebitGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SbpPaymentDebitGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'operation_id' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['operation_id'] ?? null,
            ],
            'order_id' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['order_id'] ?? null,
            ],
            'state' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['state'] ?? null,
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['description'] ?? null,
            ],
            'amount' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['amount'] ?? null,
            ],
            'commission' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['commission'] ?? null,
            ],
            'currency' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['currency'] ?? null,
            ],
            'url' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['url'] ?? null,
            ],
        ];
    }
}
