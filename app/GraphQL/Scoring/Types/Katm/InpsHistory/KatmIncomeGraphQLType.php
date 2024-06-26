<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomeDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmIncomeGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmIncomeGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm INPS Income GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'num' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'num',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->num;
                },
            ],
            'period' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'period',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->period;
                },
            ],
            'orgname' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'orgname',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->orgname;
                },
            ],
            'org_inn' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'org_inn',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->org_inn;
                },
            ],
            'income_summa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'income_summa',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->income_summa;
                },
            ],
            'inps_summa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'inps_summa',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->inps_summa;
                },
            ],
            'send_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'send_date',
                'resolve' => function (KatmIncomeDTO $root) {
                    return $root->send_date;
                },
            ],
        ];
    }
}
