<?php

namespace App\GraphQL\Common\Types;

use App\Services\Installment\DashboardService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DashboardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'DashboardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            // Cards
            'transaction' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getTransaction(),
            ],
            'installment' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getInstallment(),
            ],
            'installment_future_profit' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getInstallmentFutureProfit(),
            ],
            'installment_overdue' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentAmountsByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getInstallmentOverdue(),
            ],
            'npl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getNpl(),
            ],
            'enpl' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentNplByIncreasingValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getEnpl(),
            ],
            // Graphs
            'installment_info_by_status' => [
                'type' => Type::listOf(GraphQL::type(InstallmentMonthlyInfoGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getInstallmentInfoByStatus(),
            ],
            // Tables
            'installment_overdue_by_range' => [
                'type' => Type::listOf(GraphQL::type(InstallmentOverdueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getInstallmentOverdues(),
            ],
            // Charts
            'transaction_by_type' => [
                'type' => Type::listOf(GraphQL::type(ChartItemGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getTransactionChart(),
            ],
            'installment_by_age' => [
                'type' => Type::listOf(GraphQL::type(ChartItemGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getActiveInstallmentsByAge(),
            ],
            'installment_overdue_by_age' => [
                'type' => Type::listOf(GraphQL::type(ChartItemGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DashboardService $service) => $service->getOverdueInstallmentsByAge(),
            ],
        ];
    }
}
