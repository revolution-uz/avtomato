<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmInpsHistoryResponseDTO;
use App\GraphQL\Scoring\Types\Katm\KatmClientGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmInpsHistoryResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'KatmInpsHistoryResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Inps History Response GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'client' => [
                'type' => Type::nonNull(GraphQL::type(KatmClientGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Client',
                'resolve' => function (KatmInpsHistoryResponseDTO $root) {
                    return $root->client;
                },
            ],
            'incomes_period' => [
                'type' => Type::nonNull(GraphQL::type(KatmIncomesPeriodGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Incomes period',
                'resolve' => function (KatmInpsHistoryResponseDTO $root) {
                    return $root->incomes_period;
                },
            ],
            'incomes' => [
                'type' => Type::nonNull(GraphQL::type(KatmIncomesGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Incomes period',
                'resolve' => function (KatmInpsHistoryResponseDTO $root) {
                    return $root->incomes;
                },
            ],
            'presence_reports' => [
                'type' => Type::nonNull(GraphQL::type(KatmPresenceReportsGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Presence reports',
                'resolve' => function (KatmInpsHistoryResponseDTO $root) {
                    return $root->presence_reports;
                },
            ],
        ];
    }
}
