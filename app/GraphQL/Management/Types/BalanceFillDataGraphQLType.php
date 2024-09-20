<?php

namespace App\GraphQL\Management\Types;

use App\DTOs\Atmos\CardPay\AtmosCreatePaymentResponseDTO;
use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BalanceFillDataGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BalanceFillDataGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'transaction_id' => [
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'resolve' => fn (AtmosCreatePaymentResponseDTO $root) => $root->transaction_id,
            ],
            'result_code' => [
                'type' => Type::string(),
                'resolve' => fn (AtmosCreatePaymentResponseDTO $root) => $root->result->code,
            ],
            'result_description' => [
                'type' => Type::string(),
                'resolve' => fn (AtmosCreatePaymentResponseDTO $root) => $root->result->description,
            ],
        ];
    }
}
