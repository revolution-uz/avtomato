<?php

namespace App\GraphQL\Scoring\Types\Katm\CreditHistory;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryAdditionalInfoDTO;
use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmCreditHistoryAdditionalInfoGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'KatmCreditHistoryAdditionalInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Credit History Additional Info GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'current_debt' => [
                'type' => Type::nonNull(GraphQL::type(KatmCreditHistoryDebtGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Current Debt',
                'resolve' => function (KatmCreditHistoryAdditionalInfoDTO $root) {
                    return $root->current_debt;
                },
            ],
            'closed_debt' => [
                'type' => Type::nonNull(GraphQL::type(KatmCreditHistoryDebtGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Closed Debt',
                'resolve' => function (KatmCreditHistoryAdditionalInfoDTO $root) {
                    return $root->closed_debt;
                },
            ],
            'average_monthly_payment' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'description' => 'average_monthly_payment',
                'resolve' => function (KatmCreditHistoryAdditionalInfoDTO $root) {
                    return $root->average_monthly_payment;
                },
            ],
            'notifications' => [
                'type' => Type::nonNull(Type::listOf(Type::string())),
                'description' => 'notifications',
                'resolve' => function (KatmCreditHistoryAdditionalInfoDTO $root) {
                    $result = [];

                    foreach ($root->notifications as $notification) {
                        if (is_string($notification)) {
                            $result[] = $notification;
                        } elseif (is_array($notification)) {
                            foreach ($notification as $item) {
                                if (is_string($item)) {
                                    $result[] = $item;
                                }
                            }
                        }
                    }

                    return $result;
                },
            ],
        ];
    }
}
