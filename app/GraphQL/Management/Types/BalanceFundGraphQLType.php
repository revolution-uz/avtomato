<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\BalanceFund;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
                'resolve' => fn (BalanceFund $root) => $root->id,
            ],
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (BalanceFund $root) => $root->admin_id,
            ],
            'admin' => [
                'type' => GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (BalanceFund $root) => $root->admin,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (BalanceFund $root) => $root->amount,
            ],
            'phone_number' => [
                'type' => Type::string(),
                'resolve' => fn (BalanceFund $root) => $root->phone_number,
            ],
            'external_transaction_id' => [
                'type' => Type::string(),
                'resolve' => fn (BalanceFund $root) => $root->external_transaction_id,
            ],
            'card_pan' => [
                'type' => Type::string(),
                'resolve' => fn (BalanceFund $root) => $root->card_pan,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (BalanceFund $root) => $root->created_at->timestamp,
            ],
        ];
    }
}
