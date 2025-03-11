<?php

namespace App\GraphQL\QrPay\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SbpPaymentCreditGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SbpPaymentCreditGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'ext_id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['ext_id'],
            ],
            'state' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['state'] ?? null,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (array $root) => $root['amount'],
            ],
            'currency' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['currency'],
            ],
        ];
    }
}
