<?php

namespace App\GraphQL\Scoring\Types\PensionHistory;

use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionRowDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPensionRowGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmPensionRowGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Pension Row GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'period' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'period',
                'resolve' => function (KatmPensionRowDTO $root) {
                    return $root->period;
                },
            ],
            'month' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'month',
                'resolve' => function (KatmPensionRowDTO $root) {
                    return $root->month;
                },
            ],
            'summa' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'summa',
                'resolve' => function (KatmPensionRowDTO $root) {
                    return $root->summa;
                },
            ],
        ];
    }
}
