<?php

namespace App\GraphQL\QrPay\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SbpPaymentReceiverGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SbpPaymentReceiverGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'owner' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['owner'],
            ],
            'expire' => [
                'type' => Type::string(),
                'resolve' => fn (array $root) => $root['expire'],
            ],
        ];
    }
}
