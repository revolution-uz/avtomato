<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\BalanceFund;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BalanceFundGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BalanceFundGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => BalanceFund::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceFund $root) {
                    return $root->id;
                },
            ],
            'amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceFund $root) {
                    return $root->amount;
                },
            ],
            'phone_number' => [
                'type' => Type::string(),
                'resolve' => function (BalanceFund $root) {
                    return $root->phone_number;
                },
            ],
            'external_transaction_id' => [
                'type' => Type::string(),
                'resolve' => function (BalanceFund $root) {
                    return $root->external_transaction_id;
                },
            ],
            'card_number' => [
                'type' => Type::string(),
                'resolve' => function (BalanceFund $root) {
                    return $root->card_number;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceFund $root) {
                    return $root->created_at->timestamp;
                },
            ],
        ];
    }
}
