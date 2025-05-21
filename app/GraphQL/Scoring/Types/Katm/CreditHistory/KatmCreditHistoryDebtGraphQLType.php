<?php

namespace App\GraphQL\Scoring\Types\Katm\CreditHistory;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryDebtDTO;
use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmCreditHistoryDebtGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'KatmCreditHistoryDebtGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Credit History Debt GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'all_delays_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'all delays qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->all_delays_qty;
                },
            ],
            'all_delays_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'all_delays_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->all_delays_sum;
                },
            ],
            'contracts_wo_delays_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'contracts_wo_delays_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->contracts_wo_delays_qty;
                },
            ],
            'contracts_wo_delays_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'contracts_wo_delays_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->contracts_wo_delays_sum;
                },
            ],
            'expired_percent_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'expired_percent_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->expired_percent_qty;
                },
            ],
            'expired_percent_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'expired_percent_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->expired_percent_sum;
                },
            ],
            'revised_contracts_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'revised_contracts_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->revised_contracts_qty;
                },
            ],
            'revised_contracts_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'revised_contracts_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->revised_contracts_sum;
                },
            ],
            'delays_less_30_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_less_30_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_30_qty;
                },
            ],
            'delays_less_30_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_less_30_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_30_sum;
                },
            ],
            'switched_month_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'switched_month_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->switched_month_qty;
                },
            ],
            'switched_month_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'switched_month_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->switched_month_sum;
                },
            ],
            'delays_less_60_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_less_60_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_60_qty;
                },
            ],
            'delays_less_60_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_less_60_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_60_sum;
                },
            ],
            'delays_less_90_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_less_90_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_90_qty;
                },
            ],
            'delays_less_90_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_less_90_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_less_90_sum;
                },
            ],
            'delays_more_90_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_more_90_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_more_90_qty;
                },
            ],
            'delays_more_90_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_more_90_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_more_90_sum;
                },
            ],
            'delays_in_court_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_in_court_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_in_court_qty;
                },
            ],
            'delays_in_court_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_in_court_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_in_court_sum;
                },
            ],
            'delays_decommissioned_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_decommissioned_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_decommissioned_qty;
                },
            ],
            'delays_decommissioned_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_decommissioned_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_decommissioned_sum;
                },
            ],
            'delays_decommissioned_pcnt_qty' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'delays_decommissioned_pcnt_qty',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_decommissioned_pcnt_qty;
                },
            ],
            'delays_decommissioned_pcnt_sum' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'delays_decommissioned_pcnt_sum',
                'resolve' => function (KatmCreditHistoryDebtDTO $root) {
                    return $root->delays_decommissioned_pcnt_sum;
                },
            ],
        ];
    }
}
