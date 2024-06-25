<?php

namespace App\GraphQL\Tariff\Types;

use App\Models\Company\Tariff;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TariffGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TariffGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Tariff $root) => $root->name,
            ],
            'is_annual' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Tariff $root) => $root->is_annual,
            ],

            'percent' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Tariff $root) => $root->percent,
            ],
            'min_period' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->min_amount,
            ],
            'max_period' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->max_amount,
            ],
            'min_amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->min_amount,
            ],
            'max_amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->max_amount,
            ],
            'min_initial_fee' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->min_initial_fee,
            ],
            'max_initial_fee' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Tariff $root) => $root->max_initial_fee,
            ],
        ];
    }
}
