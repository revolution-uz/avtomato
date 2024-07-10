<?php

namespace App\GraphQL\Scoring\Types\Card;

use App\DTOs\Scoring\Card\DetailedAnalyticsInMonthDTO;
use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardMonthlyDetailedAnalyticsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'CardMonthlyDetailedAnalyticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (DetailedAnalyticsInMonthDTO $root) {
                    return $root->description;
                },
            ],
            'type_code' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (DetailedAnalyticsInMonthDTO $root) {
                    return $root->type_code;
                },
            ],
            'amount' => [
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'resolve' => function (DetailedAnalyticsInMonthDTO $root) {
                    return $root->amount;
                },
            ],
        ];
    }
}
