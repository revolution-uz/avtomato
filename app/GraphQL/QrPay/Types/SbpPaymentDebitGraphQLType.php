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
                'resolve' => fn (array $root) => $root['operation_id'],
            ],
            'order_id' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['order_id'],
            ],
            'state' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['state'],
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['description'],
            ],
            'amount' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['amount'],
            ],
            'commission' => [
                'type' => Type::float(),
                'resolve' => fn (array $root) => $root['commission'],
            ],
            'currency' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['currency'],
            ],
            'url' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['url'],
            ],
        ];
    }
}
