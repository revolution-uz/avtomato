<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Services\LoyaltyProgram\LoyaltyProgramStatisticsService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoyaltyProgramStatisticsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'LoyaltyProgramStatisticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            // Cards
            'users' => [
                'type' => Type::nonNull(GraphQL::type(LoyaltyProgramStatisticsCardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramStatisticsService $service) => $service->getUsers(),
            ],
            'subscribed_users' => [
                'type' => Type::nonNull(GraphQL::type(LoyaltyProgramStatisticsCardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramStatisticsService $service) => $service->getSubscribedUsers(),
            ],
            'extended_subscription_users' => [
                'type' => Type::nonNull(GraphQL::type(LoyaltyProgramStatisticsCardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramStatisticsService $service) => $service->getExtendedSubscriptionUsers(),
            ],
            'non_extended_subscription_users' => [
                'type' => Type::nonNull(GraphQL::type(LoyaltyProgramStatisticsCardGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramStatisticsService $service) => $service->getNonExtendedSubscriptionUsers(),
            ],
            // Chart
            'chart' => [
                'type' => Type::listOf(GraphQL::type(LoyaltyProgramStatisticsChartGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (LoyaltyProgramStatisticsService $service) => $service->getChartData(),
            ],
        ];
    }
}
