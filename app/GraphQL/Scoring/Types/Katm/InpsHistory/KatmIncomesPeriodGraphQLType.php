<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomesPeriodDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmIncomesPeriodGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'KatmIncomesPeriodGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm INPS Incomes Period GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'incomes_period_begin' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Incomes period begin',
                'resolve' => function (KatmIncomesPeriodDTO $root) {
                    return $root->incomes_period_begin;
                },
            ],
            'incomes_period_end' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Incomes period end',
                'resolve' => function (KatmIncomesPeriodDTO $root) {
                    return $root->incomes_period_end;
                },
            ],
            'incomes_all_summa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Incomes all summa',
                'resolve' => function (KatmIncomesPeriodDTO $root) {
                    return $root->incomes_all_summa;
                },
            ],
        ];
    }
}
