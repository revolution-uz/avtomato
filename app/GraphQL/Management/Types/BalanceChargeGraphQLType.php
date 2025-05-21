<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\BalanceCharge;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BalanceChargeGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BalanceChargeGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => BalanceCharge::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceCharge $root) {
                    return $root->id;
                },
            ],
            'amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceCharge $root) {
                    return $root->amount;
                },
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => function (BalanceCharge $root) {
                    return trans('feature.' . $root->feature->name->value) . ', ID=' . $root->chargeable_id;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (BalanceCharge $root) {
                    return $root->created_at->timestamp;
                },
            ],
        ];
    }
}
