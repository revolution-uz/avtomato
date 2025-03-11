<?php

namespace App\GraphQL\QrPay\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SbpPaymentSenderGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SbpPaymentSenderGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'number' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['number'] ?? null,
            ],
            'expire' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['expire'] ?? null,
            ],
        ];
    }
}
