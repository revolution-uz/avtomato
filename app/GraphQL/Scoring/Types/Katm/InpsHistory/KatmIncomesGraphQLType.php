<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomesDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmIncomesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmIncomesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm INPS Incomes GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'income' => [
                'type' => Type::listOf(GraphQL::type(KatmIncomeGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'income',
                'resolve' => function (KatmIncomesDTO $root) {
                    return $root->income;
                },
            ],
        ];
    }
}
